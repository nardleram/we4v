<?php

namespace App\Http\Middleware;

use App\Models\Team;
use App\Models\User;
use App\Models\Group;
use App\Models\Image;
use Inertia\Middleware;
use App\Models\Membership;
use App\Models\Association;
use Illuminate\Http\Request;
use App\Actions\Users\GetUsers;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

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
            'authUser' => fn () => $request->user()
                ? $request->user()->only('id', 'email', 'name', 'surname', 'username')
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
                        $users = User::whereIn('id', $assoc_ids)->get(['id', 'username']);
                        
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
                                    $pending_reqs[$a]['requested_by'] = $assocs[$a]->requested_by;
                                    $pending_reqs[$a]['id'] = $assocs[$a]->id;
                                }
                                if ($assocs[$a]->requested_of === $users[$u]->id) {
                                    $pending_reqs[$a]['requestee'] = $users[$u]->username;
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
                            ->join('users', function ($join) {
                                $join->on('users.id', '=', 'groups.owner');
                            })
                            ->join('memberships', function ($join) {
                                $join->on('memberships.membershipable_id', '=', 'groups.id')
                                ->where('memberships.user_id', auth()->id());
                            })
                            ->select([
                                'groups.name as group_name',
                                'groups.id as group_id',
                                'groups.description as group_description',
                                'groups.geog_area as geog_area',
                                'users.username as username',
                                'users.id as g_user_id',
                                'memberships.role as g_role'
                            ])
                            ->orderBy('groups.name')
                            ->get();
                        
                        $rawTeams = Team::whereIn('teams.id', $teamIds)
                            ->join('users', function ($join) {
                                $join->on('users.id', '=', 'teams.owner');
                            })
                            ->join('memberships', function ($join) {
                                $join->on('memberships.membershipable_id', '=', 'teams.id')
                                ->where('memberships.user_id', auth()->id());
                            })
                            ->select([
                                'teams.name as team_name',
                                'teams.id as team_id',
                                'teams.function as team_function',
                                'users.username as username',
                                'users.id as t_user_id',
                                'memberships.role as t_role'
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
                            $membReqs[$membCount]['groupRequester'] = $rawGroup->username;
                            $membReqs[$membCount]['groupRequesterId'] = $rawGroup->g_user_id;
                            $membReqs[$membCount]['gRole'] = $rawGroup->g_role;
                            $membReqs[$membCount]['type'] = 'group';
                            $membReqs[$membCount]['count'] = $membCount;
                            ++$membCount;
                        }

                        foreach ($rawTeams as $rawTeam) {
                            $membReqs[$membCount]['teamName'] = $rawTeam->team_name;
                            $membReqs[$membCount]['teamId'] = $rawTeam->team_id;
                            $membReqs[$membCount]['teamFunc'] = $rawTeam->team_function;
                            $membReqs[$membCount]['teamRequester'] = $rawTeam->username;
                            $membReqs[$membCount]['teamRequesterId'] = $rawTeam->t_user_id;
                            $membReqs[$membCount]['tRole'] = $rawTeam->t_role;
                            $membReqs[$membCount]['type'] = 'team';
                            $membReqs[$membCount]['count'] = $membCount;
                            ++$membCount;
                        }

                        return $membReqs;
                    }
                }

                return [];
            }

        ]);
    }
}
