<?php

namespace App\Models;

use stdClass;
use Carbon\Carbon;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuids;

    protected $fillable = [
        'membershipable_id', 
        'membershipable_type', 
        'created_at',
        'updated_by',
        'user_id', 
        'group_id', 
        'role', 
        'confirmed', 
        'is_admin'
    ];
    protected $casts = ['is_admin' => 'boolean'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function membershipable() : object
    {
        return $this->morphTo();
    }

    public function member() : object
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updatedBy() : object
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function networkMember() : object
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function tasks() : object
    {
        return $this->hasMany(Task::class, 'id', 'membershipable_id');
    }

    public static function getPendingMemberships() : object
    {
        return (new static())
            ->where('confirmed', false)
            ->where('user_id', auth()->id())
            ->get(['membershipable_id', 'membershipable_type', 'role']);
    }

    public static function getMemberships() : object
    {
        return (new static())
            ->where('confirmed', true)
            ->where('user_id', auth()->id())
            ->get(['membershipable_id']);
    }

    public static function getMemberships4Votes() : array
    {
        $ids = [];

        if (auth()->id()) {
            $memberships = Membership::where('confirmed', true)
                ->where('user_id', auth()->id())
                ->get(['membershipable_id', 'membershipable_type']);

            foreach($memberships as $membership) {
                array_push($ids, $membership->membershipable_id);

                // Votes assigned to groups must ALSO generate invites for associates that are memebers of that group's teams.
                // So also get those groups that house my team memberships
                if ($membership->membershipable_type === 'App\Models\Team') {
                    $group = $membership->membershipable->group;

                    !in_array($group->id, $ids) 
                    ? array_push($ids, $group->id)
                    : null;
                } 
            }
        }

        return $ids;
    }

    public static function getTaskMemberships($userId) : object
    {
        $teamIds = [];
        $taskIds = [];

        if (auth()->id()) {
            $myTeams = Membership::where('membershipable_type', 'App\\Models\\Team')
                ->where('user_id', $userId)
                ->get('membershipable_id');

            foreach($myTeams as $team) {
                array_push($teamIds, $team->membershipable_id);
            }

            $tasks = Task::whereIn('taskable_id', $teamIds)
                ->where('completed', false)
                ->get('id');

            foreach($tasks as $task) {
                array_push($taskIds, $task->id);
            }

            return Membership::where('memberships.user_id', $userId)
                ->where('membershipable_type', 'App\Models\Task')
                ->orWhere(function ($query) use ($taskIds) {
                    $query->whereIn('membershipable_id', $taskIds)
                    ->where('memberships.user_id', null);
                })
                ->join('tasks AS Ta', function($join) {
                    $join->on('Ta.id', '=', 'membershipable_id')
                    ->where('Ta.completed', '=', 'f');
                })
                ->leftJoin('teams AS Te', function($join) {
                    $join->on('Te.id', '=', 'Ta.taskable_id');
                })
                ->leftJoin('groups AS Gr', function($join) {
                    $join->on('Gr.id', '=', 'Ta.taskable_id');
                })
                ->leftJoin('projects AS Pr', function($join) {
                    $join->on('Pr.id', '=', 'Ta.project_id');
                })
                ->leftJoin('notes AS No1', function($join) {
                    $join->on('No1.noteable_id', '=', 'Ta.id');
                })
                ->leftJoin('notes AS No2', function($join) {
                    $join->on('No2.noteable_id', '=', 'Pr.id');
                })
                ->leftJoin('users AS Us1', function($join) {
                    $join->on('Us1.id', '=', 'No1.user_id');
                })
                ->leftJoin('users AS Us2', function($join) {
                    $join->on('Us2.id', '=', 'No2.user_id');
                })
                ->leftJoin('users AS Us3', function ($join) {
                    $join->on('Us3.id', '=', 'Ta.owner');
                })
                ->select([
                    'memberships.membershipable_id',
                    'memberships.id AS membership_id',
                    'memberships.user_id AS membership_user_id',
                    'Ta.id AS task_id',
                    'Ta.taskable_id as taskable_id',
                    'Ta.taskable_type as taskable_type',
                    'Ta.completed AS completed',
                    'Ta.description AS task_description',
                    'Ta.end_date AS task_end_date',
                    'Ta.name AS task_name',
                    'Us3.username AS task_owner',
                    'Te.name AS team_name',
                    'Gr.name AS group_name',
                    'Pr.id AS project_id',
                    'Pr.name AS project_name',
                    'No1.id AS task_note_id',
                    'No1.noteable_id AS task_noteable_id',
                    'No1.body AS task_note_body',
                    'No1.created_at AS task_note_created_at',
                    'No2.id AS project_note_id',
                    'No2.noteable_id AS project_noteable_id',
                    'No2.body AS project_note_body',
                    'No2.created_at AS project_note_created_at',
                    'Us1.username AS task_note_author',
                    'Us2.username AS project_note_author',
                    'Us3.username AS task_owner'
                ])
                ->groupBy([
                    'Ta.id',
                    'Gr.name',
                    'Te.name',
                    'No1.created_at',
                    'No2.created_at',
                    'No1.id',
                    'No1.body',
                    'No2.id',
                    'No2.body',
                    'Pr.id',
                    'Us1.username',
                    'Us1.id',
                    'Us2.username',
                    'Us2.id',
                    'Us3.id',
                    'Us3.username',
                    'memberships.id',
                    'memberships.user_id',
                    'memberships.created_at',
                    'memberships.membershipable_id'
                ])
                ->orderBy('Pr.end_date')
                ->orderBy('Ta.end_date')
                ->orderBy('No1.created_at', 'ASC')
                ->orderBy('No2.created_at', 'ASC')
                ->get();
        }
        return new stdClass();
    }

    public static function compileTaskMembershipDetails($taskMembershipDetails, $taskUsernameDetails)
    {
        $mytasks = [];
        $addedMembers = [];
        $taskNoteIds = [];
        $projectNoteIds = [];
        $loop = 0;
        $taskCount = 0;
        $taskNoteCount = 0;
        $projectNoteCount = 0;
        $taskMemberCount = 0;
        $currentTaskId = 0;
        $currentTaskNoteId = 0;
        $currentProjectNoteId = 0;

        foreach($taskMembershipDetails as $task) {
            if ($loop > 0) {
                if ($currentTaskId !== $task->task_id) {
                    ++$taskCount;
                    $taskNoteCount = 0;
                    $projectNoteCount = 0;
                    $taskMemberCount = 0;
                    $addedMembers = [];
                    $taskNoteIds = [];
                    $projectNoteIds = [];
                }

                if ($currentTaskNoteId !== $task->task_note_id) {
                    ++$taskNoteCount;
                }

                if ($currentProjectNoteId !== $task->project_note_id) {
                    ++$projectNoteCount;
                }
            }

            if ($currentTaskId !== $task->task_id && !$task->completed) {
                $mytasks[$taskCount]['name'] = $task->task_name;
                $mytasks[$taskCount]['taskable_id'] = $task->taskable_id;
                $mytasks[$taskCount]['taskable_type'] = $task->taskable_type;
                $mytasks[$taskCount]['id'] = $task->task_id;
                $mytasks[$taskCount]['end_date'] = Carbon::parse($task->task_end_date)->format('d M y');
                $mytasks[$taskCount]['input_end_date'] = $task->task_end_date;
                $mytasks[$taskCount]['completed'] = $task->completed;
                $mytasks[$taskCount]['description'] = $task->task_description;
                $mytasks[$taskCount]['owner'] = $task->task_owner;
                $mytasks[$taskCount]['group_name'] = $task->group_name;
                $mytasks[$taskCount]['team_name'] = $task->team_name;
                $mytasks[$taskCount]['project_id'] = $task->project_id;
                $mytasks[$taskCount]['project_name'] = $task->project_name;
            }

            if ($task->task_id === $task->task_noteable_id && !$task->completed) {
                if (!in_array($task->task_note_id, $taskNoteIds)) {
                    $mytasks[$taskCount]['task_notes'][$taskNoteCount]['task_note_id'] = $task->task_note_id;
                    $mytasks[$taskCount]['task_notes'][$taskNoteCount]['task_note_body'] = $task->task_note_body;
                    $mytasks[$taskCount]['task_notes'][$taskNoteCount]['task_note_author'] = $task->task_note_author;
                    $mytasks[$taskCount]['task_notes'][$taskNoteCount]['task_note_created_at'] = Carbon::parse($task->task_note_created_at)->format('d M y, H:i');

                    array_push($taskNoteIds, $task->task_note_id);
                }
            }

            if ($task->project_id === $task->project_noteable_id && !$task->completed) {
                if (!in_array($task->project_note_id, $projectNoteIds)) {
                    $mytasks[$taskCount]['project_notes'][$projectNoteCount]['project_note_id'] = $task->project_note_id;
                    $mytasks[$taskCount]['project_notes'][$projectNoteCount]['project_note_body'] = $task->project_note_body;
                    $mytasks[$taskCount]['project_notes'][$projectNoteCount]['project_note_author'] = $task->project_note_author;
                    $mytasks[$taskCount]['project_notes'][$projectNoteCount]['project_note_created_at'] = Carbon::parse($task->project_note_created_at)->format('d M y, H:i');

                    array_push($projectNoteIds, $task->project_note_id);
                }
            }

            foreach($taskUsernameDetails as $usernameDetails) {
                if (!$task->completed && ($task->task_id === $usernameDetails->task_id) && !in_array($usernameDetails->username, $addedMembers) && $usernameDetails->username) {
                    array_push($addedMembers, $usernameDetails->username);
                    $mytasks[$taskCount]['task_members'][$taskMemberCount]['username'] = $usernameDetails->username;
                    ++$taskMemberCount;
                }
            }

            $currentTaskId = $task->task_id;
            $currentTaskNoteId = $task->task_note_id;
            $currentProjectNoteId = $task->project_note_id;
            ++$loop;
        }
        
        return $mytasks;
    }

    public static function getUserMemberships($userId) : array
    {
        $rawMemberships = Membership::where('memberships.user_id', $userId)
            ->where('memberships.confirmed', true)
            ->leftJoin('groups', function($join) {
                $join->on('groups.id', '=', 'memberships.membershipable_id');
            })
            ->leftJoin('teams', function($join) {
                $join->on('teams.id', '=', 'memberships.membershipable_id');
            })
            ->leftJoin('teams AS Te1', function($join) {
                $join->on('Te1.group_id', '=', 'groups.id');
            })
            ->leftJoin('projects', function($join) {
                $join->on('projects.projectable_id', '=', 'groups.id')
                ->orOn('projects.projectable_id', '=', 'teams.id');
            })
            ->leftJoin('tasks', function($join) {
                $join->on('tasks.project_id', '=', 'projects.id');
            })
            ->leftJoin('memberships AS Me1', function($join) {
                $join->on('Me1.membershipable_id', '=', 'memberships.membershipable_id');
            })
            ->leftJoin('memberships AS Me2', function($join) {
                $join->on('Me2.membershipable_id', '=', 'Te1.id');
            })
            ->leftJoin('users AS Us1', function($join) {
                $join->on('Us1.id', '=', 'groups.owner');
            })
            ->leftJoin('users AS Us2', function($join) {
                $join->on('Us2.id', '=', 'teams.owner');
            })
            ->leftJoin('users AS Us3', function($join) {
                $join->on('Us3.id', '=', 'Me1.user_id');
            })
            ->leftJoin('users AS Us4', function($join) {
                $join->on('Us4.id', '=', 'Me2.user_id');
            })
            ->leftJoin('groups AS Gr1', function($join) {
                $join->on('Gr1.id', '=', 'teams.group_id');
            })
            ->select([
                'memberships.id as membership_id',
                'memberships.membershipable_type as membership_type',
                'memberships.role as membership_role',
                'memberships.is_admin as is_admin',
                'memberships.user_id as user_id',
                'memberships.membershipable_id as membershipable_id',
                'groups.id as group_id',
                'groups.name as group_name',
                'groups.description as group_description',
                'Gr1.name as team_group_name',
                'Gr1.id as team_group_id',
                'Te1.id as group_team_id',
                'Te1.group_id as group_team_group_id',
                'Te1.name as group_team_name',
                'Te1.function as group_team_function',
                'Me2.user_id as group_team_user_id',
                'Me2.membershipable_id as membership_group_team_id',
                'Me2.role as group_team_user_role',
                'Me2.is_admin as group_team_admin',
                'Us4.username as group_team_member',
                'Us4.slug as group_team_member_slug',
                'teams.name as team_name',
                'teams.function as team_function',
                'teams.id as team_id',
                'projects.id as project_id',
                'projects.name as project_name',
                'projects.description as project_description',
                'projects.end_date as project_end_date',
                'projects.completed as project_completed',
                'projects.projectable_id as projectable_id',
                'tasks.id as task_id',
                'tasks.name as task_name',
                'tasks.description as task_description',
                'tasks.end_date as task_end_date',
                'tasks.completed as task_completed',
                'tasks.project_id as task_project_id',
                'Us1.username as group_owner',
                'Us1.slug as group_slug',
                'Us2.username as team_owner',
                'Us2.slug as team_slug',
                'Me1.user_id as fellow_membership_user_id',
                'Me1.membershipable_id as fellow_membership_group_team_id',
                'Me1.role as fellow_membership_role',
                'Me1.is_admin as fellow_membership_admin',
                'Us3.username as fellow_member',
                'Us3.slug as fellow_member_slug',
            ])
            ->groupBy([
                'memberships.id',
                'memberships.membershipable_id',
                'groups.name',
                'groups.id',
                'Us3.username',
                'Us3.slug',
                'teams.name',
                'teams.id',
                'Te1.name',
                'Te1.id',
                'projects.id',
                'tasks.project_id',
                'tasks.id',
                'Us1.username',
                'Us2.username',
                'Us4.username',
                'Us1.slug',
                'Us2.slug',
                'Us4.slug',
                'Us3.id',
                'Us4.id',
                'Me1.user_id',
                'Me1.membershipable_id',
                'Me1.is_admin',
                'Me1.role',
                'Me2.user_id',
                'Me2.membershipable_id',
                'Me2.is_admin',
                'Me2.role',
                'Te1.function',
                'Gr1.name',
                'Gr1.id'
            ])
            ->orderBy('groups.name')
            ->orderBy('Te1.name')
            ->orderBy('projects.name')
            ->orderBy('teams.name')
            ->orderBy('Us3.username')
            ->orderBy('Us4.username')
            ->get();

        $memberships = [];
        $fellowMembers = [];
        $groupTeamIds = [];
        $groupTeamUserIds = [];
        $projectIds = [];
        $taskIds = [];
        $loop = 0;
        $currentGroupTeamId = 0;
        $currentProjectId = 0;
        $currentTaskId = 0;
        $currentMembershipableId = 0;
        $currentGroupTeamUserId = 0;
        $membershipCount = 0;
        $fellowMemberCount = 0;
        $groupTeamCount = 0;
        $groupTeamMemberCount = 0;
        $projectCount = 0;
        $taskCount = 0;

        foreach($rawMemberships as $rawMembership) {
            if ($rawMembership->project_id !== $currentProjectId && $loop > 0) {
                $taskCount = 0;
                ++$projectCount;
            }

            if ($rawMembership->task_id !== $currentTaskId && $loop > 0) {
                ++$taskCount;
            }

            if ($rawMembership->group_team_id !== $currentGroupTeamId && $loop > 0) {
                ++$groupTeamCount;
                $groupTeamMemberCount = 0;
                $groupTeamUserIds = [];
            }

            if ($rawMembership->group_team_user_id !== $currentGroupTeamUserId && $rawMembership->group_team_id === $currentGroupTeamId) {
                ++$groupTeamMemberCount;
            }

            if ($rawMembership->membershipable_id !== $currentMembershipableId) {
                if ($loop > 0) {
                    ++$membershipCount;
                    $fellowMemberCount = 0;
                    $projectCount = 0;
                    $taskCount = 0;
                    $groupTeamCount = 0;
                    $groupTeamMemberCount = 0;
                    $fellowMembers = [];
                }

                // Compile base membership data once only
                if ($rawMembership->membership_type === 'App\\Models\\Group') {
                    $memberships[$membershipCount]['description'] = $rawMembership->group_description;
                    $memberships[$membershipCount]['name'] = $rawMembership->group_name;
                    $memberships[$membershipCount]['owner'] = $rawMembership->group_owner;
                    $memberships[$membershipCount]['slug'] = $rawMembership->group_slug;
                    $memberships[$membershipCount]['role'] = $rawMembership->membership_role;
                    $memberships[$membershipCount]['admin'] = $rawMembership->is_admin;
                    $memberships[$membershipCount]['type'] = 'Group';
                }
    
                if ($rawMembership->membership_type === 'App\\Models\\Team') {
                    $memberships[$membershipCount]['name'] = $rawMembership->team_name;
                    $memberships[$membershipCount]['owner'] = $rawMembership->team_owner;
                    $memberships[$membershipCount]['function'] = $rawMembership->team_function;
                    $memberships[$membershipCount]['slug'] = $rawMembership->team_slug;
                    $memberships[$membershipCount]['role'] = $rawMembership->membership_role;
                    $memberships[$membershipCount]['admin'] = $rawMembership->is_admin;
                    $memberships[$membershipCount]['team_group_name'] = $rawMembership->team_group_name;
                    $memberships[$membershipCount]['type'] = 'Team';
                }
            }

            // Group/team has project(s)
            if ( $rawMembership->project_id && ($rawMembership->project_id !== $currentProjectId) ) {
                if (!in_array($rawMembership->project_id, $projectIds)) {
                    array_push($projectIds, $rawMembership->project_id);

                    $memberships[$membershipCount]['projects'][$projectCount]['project_name'] = $rawMembership->project_name;
                    $memberships[$membershipCount]['projects'][$projectCount]['project_description'] = $rawMembership->project_description;
                    $memberships[$membershipCount]['projects'][$projectCount]['project_completed'] = $rawMembership->project_completed;
                    $memberships[$membershipCount]['projects'][$projectCount]['project_end_date'] = Carbon::parse($rawMembership->project_end_date)->format('d M Y');
                }
            }

            // Project has task(s)
            if ( $rawMembership->task_id 
                // && ($rawMembership->project_id === $currentProjectId) 
                && ($rawMembership->taskId !== $currentTaskId)
                && ($rawMembership->task_project_id === $currentProjectId) 
            ) {
                if (!in_array($rawMembership->task_id, $taskIds)) {
                    array_push($taskIds, $rawMembership->task_id);

                    $memberships[$membershipCount]['projects'][$projectCount]['tasks'][$taskCount]['task_name'] = $rawMembership->task_name;
                    $memberships[$membershipCount]['projects'][$projectCount]['tasks'][$taskCount]['task_description'] = $rawMembership->task_description;
                    $memberships[$membershipCount]['projects'][$projectCount]['tasks'][$taskCount]['task_completed'] = $rawMembership->task_completed;
                    $memberships[$membershipCount]['projects'][$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawMembership->task_end_date)->format('d M Y');

                    ++$taskCount;
                }
            }

            // Membership is group and it has team(s)
            if ( $rawMembership->membership_type === 'App\\Models\\Group' && ($rawMembership->group_team_group_id === $rawMembership->group_id) ) {
                if (!in_array($rawMembership->group_team_id, $groupTeamIds)) { // Group's teams
                    array_push($groupTeamIds, $rawMembership->group_team_id);

                    $memberships[$membershipCount]['group_teams'][$groupTeamCount]['name'] = $rawMembership->group_team_name;
                    $memberships[$membershipCount]['group_teams'][$groupTeamCount]['function'] = $rawMembership->group_team_function;
                }

                if (!in_array($rawMembership->group_team_user_id, $groupTeamUserIds)) { // Group's teams' members
                    array_push($groupTeamUserIds, $rawMembership->group_team_user_id);

                    $memberships[$membershipCount]['group_teams'][$groupTeamCount]['members'][$groupTeamMemberCount]['username'] = $rawMembership->group_team_member;
                    $memberships[$membershipCount]['group_teams'][$groupTeamCount]['members'][$groupTeamMemberCount]['slug'] = $rawMembership->group_team_member_slug;
                    $memberships[$membershipCount]['group_teams'][$groupTeamCount]['members'][$groupTeamMemberCount]['role'] = $rawMembership->group_team_user_role;
                    $memberships[$membershipCount]['group_teams'][$groupTeamCount]['members'][$groupTeamMemberCount]['admin'] = $rawMembership->group_team_admin;

                    ++$groupTeamMemberCount;
                    +$groupTeamCount;
                }
            }

            // Group/team fellow members
            if ( ($rawMembership->fellow_membership_user_id !== auth()->id()) 
                && (!in_array($rawMembership->fellow_membership_user_id, $fellowMembers)) ) { 
                array_push($fellowMembers, $rawMembership->fellow_membership_user_id);

                $memberships[$membershipCount]['fellow_members'][$fellowMemberCount]['username'] = $rawMembership->fellow_member;
                $memberships[$membershipCount]['fellow_members'][$fellowMemberCount]['slug'] = $rawMembership->fellow_member_slug;
                $memberships[$membershipCount]['fellow_members'][$fellowMemberCount]['role'] = $rawMembership->fellow_membership_role;
                $memberships[$membershipCount]['fellow_members'][$fellowMemberCount]['admin'] = $rawMembership->fellow_membership_admin;
                
                ++$fellowMemberCount;
            }

            ++$loop;
            $currentGroupTeamId = $rawMembership->group_team_id;
            $currentProjectId = $rawMembership->project_id;
            $currentTaskId = $rawMembership->task_id;
            $currentMembershipableId = $rawMembership->membershipable_id;
            $currentGroupTeamUserId = $rawMembership->group_team_user_id;
        }
        // dd($memberships);
        return $memberships;
    }

    public static function getAddresseeAdminGroups()
    {
        return (new static())
            ->where('membershipable_type', 'App\Models\Group')
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->join('groups', function ($join) {
                $join->on('membershipable_id', '=', 'groups.id');
            })
            ->get([
                'groups.id AS admin_group_id',
                'groups.name AS admin_group_name'
            ])->toArray();
    }

    public static function getAddresseeAdminTeams()
    {
        return (new static())
            ->where('membershipable_type', 'App\Models\Team')
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->join('teams', function ($join) {
                $join->on('membershipable_id', '=', 'teams.id');
            })
            ->get([
                'teams.name AS admin_team_name',
                'teams.id AS admin_team_id'
            ])->toArray();
    }
}