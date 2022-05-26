<?php

namespace App\Http\Middleware;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\Vote;
use App\Models\Group;
use App\Models\Image;
use App\Models\Network;
use Inertia\Middleware;
use App\Models\Membership;
use App\Models\Association;
use Illuminate\Http\Request;
use App\Actions\Users\GetUsers;
use App\Http\Resources\UserMembershipCollection;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';
    private $myImages;

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    public function share(Request $request)
    {
        $request->user()
            ? $myImages = Image::where('imageable_id', $request->user()->id)
                ->where('imageable_type', 'App\Models\User')
                ->orderBy('format', 'desc')
                ->get()
            : $myImages = 0;

        return array_merge(parent::share($request), [
            'csrf_token' => csrf_token(),

            'searchResults' => [
                'searchData' => fn () => $request->session()->get('searchData')
            ],

            'articleImageData' => [
                'articleImagePath' => fn () => $request->session()->get('articleImagePath')
            ],
            
            'authUser' => fn () => $request->user()
                ? $request->user()->only('id', 'email', 'name', 'surname', 'username', 'slug')
                : null,

            'userProfileImages' => function () use($request, $myImages)
            {
                if ($request->user()) {
                    if (count($myImages) === 1) {
                        if ($myImages[0]->format === 'profile') {
                            return [
                                'profile' => $myImages[0]->path,
                                'bkgrnd' => 'images/bkgrnd.jpg'
                            ];
                        } else {
                            return [
                                'profile' => 'images/nobody.png',
                                'bkgrnd' => $myImages[0]->path
                            ];
                        }
                    }
                    return count($myImages) === 2
                        ? [
                            'profile' => $myImages[0]->path,
                            'bkgrnd' => $myImages[1]->path
                        ]
                        : [
                            'profile' => 'images/nobody.png',
                            'bkgrnd' => 'images/bkgrnd.jpg'
                        ];
                }
            },
            
            'myAssociates' => function (GetUsers $getUserData)
                {
                    if (!auth()->id()) {
                        return [];
                    } 
                    
                    $allAssociates = Association::getAssociations();
                    $myAssociatesIds = array_diff($allAssociates, array(auth()->id()));

                    $myAssociates = $getUserData->getUsers($myAssociatesIds);

                    $loop = 0;
                    
                    foreach($myAssociates as $myAssociate) {
                        if(!$myAssociate->path) {
                            $myAssociates[$loop]['path'] = 'images/nobody.png';
                        }
                        ++$loop;
                    }
                    
                    return $myAssociates;
                },
            
            'myPendingAssocReqs' => function ()
            {
                if (request()->user()) {
                    $pending_reqs = array();

                    $assocs = Association::getPendingAssociations();
                    
                    $assoc_ids = [];
                    foreach($assocs as $assoc) {
                        array_push($assoc_ids, $assoc->requested_of);
                        array_push($assoc_ids, $assoc->requested_by);
                    }
                    
                    if (count($assoc_ids) > 0) {
                        $users = User::whereIn('id', $assoc_ids)->get(['id', 'username', 'slug']);
                        
                        $profile_pics = Image::whereIn('imageable_id', $assoc_ids)
                            ->where('format', 'profile')
                            ->get(['imageable_id', 'path']);

                        for ($a = 0; $a < count($assocs); ++$a) {
                            $pending_reqs[$a]['requester_profile_photo'] = 'images/nobody.png';
                            $pending_reqs[$a]['requestee_profile_photo'] = 'images/nobody.png';
                            for ($p = 0; $p < count($profile_pics); ++$p) {
                                if ($assocs[$a]->requested_by === $profile_pics[$p]->imageable_id) {
                                    $pending_reqs[$a]['requester_profile_photo'] = $profile_pics[$p]->path;
                                } 
                                if ($assocs[$a]->requested_of === $profile_pics[$p]->imageable_id) {
                                    $pending_reqs[$a]['requestee_profile_photo'] = $profile_pics[$p]->path;
                                }
                            }
                            for ($u = 0; $u < count($users); ++$u) {
                                if ($assocs[$a]->requested_by === $users[$u]->id) {
                                    $pending_reqs[$a]['requester'] = $users[$u]->username;
                                    $pending_reqs[$a]['slug_requester'] = $users[$u]->slug;
                                    $pending_reqs[$a]['requested_by'] = $assocs[$a]->requested_by;
                                    $pending_reqs[$a]['id'] = $assocs[$a]->id;
                                }
                                if ($assocs[$a]->requested_of === $users[$u]->id) {
                                    $pending_reqs[$a]['requestee'] = $users[$u]->username;
                                    $pending_reqs[$a]['slug_requestee'] = $users[$u]->slug;
                                    $pending_reqs[$a]['requested_of'] = $assocs[$a]->requested_of;
                                }
                            }
                        } 
                        for ($r = 0; $r < count($pending_reqs); ++$r) {
                            if (auth()->id() === $pending_reqs[$r]['requested_of']) {
                                $pending_reqs[$r]['myResponseNeeded'] = true;
                            } else {
                                $pending_reqs[$r]['myResponseNeeded'] = false;
                            }
                        }
                    }

                    return $pending_reqs;
                }
            },

            'myPendingMembReqs' => function ()
            {
                if (request()->user()) {
                    $memberships = Membership::getPendingMemberships();

                    if (count($memberships) > 0) {
                        $groupIds = [];
                        $teamIds = [];

                        foreach ($memberships as $member) {
                            if ($member->membershipable_type === 'App\Models\Group') {
                                array_push($groupIds, $member->membershipable_id);
                            }

                            if ($member->membershipable_type === 'App\Models\Team') {
                                array_push($teamIds, $member->membershipable_id);
                            }
                        };
                        
                        $rawGroups = Group::whereIn('groups.id', $groupIds)
                            ->leftJoin('users AS Us1', function ($join) {
                                $join->on('Us1.id', '=', 'groups.owner');
                            })
                            ->leftJoin('memberships AS Me', function ($join) {
                                $join->on('Me.membershipable_id', '=', 'groups.id')
                                ->where('Me.user_id', auth()->id());
                            })
                            ->leftJoin('users AS Us2', function ($join) {
                                $join->on('Us2.id', '=', 'Me.updated_by');
                            })
                            ->select([
                                'groups.name as group_name',
                                'groups.id as group_id',
                                'groups.description as group_description',
                                'groups.geog_area as geog_area',
                                'Us1.username as g_owner_username',
                                'Us1.id as g_owner_id',
                                'Me.role as g_role',
                                'Us2.username as g_updated_by',
                                'Me.id as membership_id',
                                'Me.is_admin as g_admin',
                                'Me.created_at as g_created',
                                'Me.updated_at as g_updated'
                            ])
                            ->orderBy('groups.name')
                            ->get();
                        
                        $rawTeams = Team::whereIn('teams.id', $teamIds)
                            ->leftJoin('users', function ($join) {
                                $join->on('users.id', '=', 'teams.owner');
                            })
                            ->leftJoin('memberships AS Me', function ($join) {
                                $join->on('Me.membershipable_id', '=', 'teams.id')
                                ->where('Me.user_id', auth()->id());
                            })
                            ->leftJoin('users AS Us2', function ($join) {
                                $join->on('Us2.id', '=', 'Me.updated_by');
                            })
                            ->select([
                                'teams.name as team_name',
                                'teams.id as team_id',
                                'teams.function as team_function',
                                'users.username as t_owner_username',
                                'users.id as t_owner_id',
                                'Me.role as t_role',
                                'Me.id as membership_id',
                                'Us2.username as t_updated_by',
                                'Us2.id as t_updated_by_id',
                                'Me.is_admin as t_admin',
                                'Me.created_at as t_created',
                                'Me.updated_at as t_updated'
                            ])
                            ->orderBy('teams.name')
                            ->get();
                        
                        $membReqs = [];
                        $membCount = 0;

                        foreach ($rawGroups as $rawGroup) {
                            $membReqs[$membCount]['groupName'] = $rawGroup->group_name;
                            $membReqs[$membCount]['groupId'] = $rawGroup->group_id;
                            $membReqs[$membCount]['groupDesc'] = $rawGroup->group_description;
                            $membReqs[$membCount]['geogArea'] = $rawGroup->geog_area;
                            $membReqs[$membCount]['g_updated_by'] = $rawGroup->g_updated_by;
                            // $membReqs[$membCount]['groupRequesterId'] = $rawGroup->g_updated_by_id;
                            $membReqs[$membCount]['groupOwner'] = $rawGroup->g_owner_username;
                            $membReqs[$membCount]['groupOwnerId'] = $rawGroup->g_owner_id;
                            $membReqs[$membCount]['gRole'] = $rawGroup->g_role;
                            $membReqs[$membCount]['type'] = 'group';
                            $membReqs[$membCount]['gAdmin'] = $rawGroup->g_admin;
                            $membReqs[$membCount]['membership_id'] = $rawGroup->membership_id;

                            $rawGroup->g_updated > $rawGroup->g_created
                            ? $membReqs[$membCount]['g_updated'] = true
                            : $membReqs[$membCount]['g_updated'] = false; 

                            ++$membCount;
                        }

                        foreach ($rawTeams as $rawTeam) {
                            $membReqs[$membCount]['teamName'] = $rawTeam->team_name;
                            $membReqs[$membCount]['teamId'] = $rawTeam->team_id;
                            $membReqs[$membCount]['teamFunc'] = $rawTeam->team_function;
                            $membReqs[$membCount]['t_updated_by'] = $rawTeam->t_updated_by;
                            // $membReqs[$membCount]['teamRequesterId'] = $rawTeam->t_updated_by_id;
                            $membReqs[$membCount]['teamOwner'] = $rawTeam->t_owner_username;
                            $membReqs[$membCount]['teamOwnerId'] = $rawTeam->t_owner_id;
                            $membReqs[$membCount]['tRole'] = $rawTeam->t_role;
                            $membReqs[$membCount]['type'] = 'team';
                            $membReqs[$membCount]['tAdmin'] = $rawTeam->t_admin;
                            $membReqs[$membCount]['membership_id'] = $rawTeam->membership_id;

                            $rawTeam->t_updated > $rawTeam->t_created
                            ? $membReqs[$membCount]['t_updated'] = true
                            : $membReqs[$membCount]['t_updated'] = false;
                            
                            ++$membCount;
                        }
                        
                        return $membReqs;
                    }
                }

                return [];
            },

            'myNetworks' => function ()
            {
                return Network::getMyNetworks();
            },

            'myPendingNetworkReqs' => function ()
            {
                return Group::getMyNetworkReqs();
            },

            'myPendingVotes' => function ()
            {   
                return Vote::getPendingVotes(Membership::getMemberships4Votes());
            },

            'closedVotes' => function ()
            {
                return Vote::getClosedVotes();
            },

            'myOpenTasks' => function ()
            {
                $taskMembershipDetails = Membership::getTaskMemberships(auth()->id());
            
                $taskUsernameDetails = Task::getTaskUsers($taskMembershipDetails);

                return Membership::compileTaskMembershipDetails($taskMembershipDetails, $taskUsernameDetails);
            },

            'myMemberships' => function ()
            {
                if (auth()->id()) {
                    return Membership::getUserMemberships(auth()->id());
                }
            },

        ]);
    }
}
