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

                // Votes assigned to groups must also generate invites for associates in that group's teams.
                if ($membership->membershipable_type === 'App\Models\Team') {
                    $team = Team::findOrFail($membership->membershipable_id);

                    foreach($team->group()->get('id') as $groupId) {
                        !in_array($groupId->id, $ids) 
                        ? array_push($ids, $groupId->id)
                        : null;
                    }
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
                    'membershipable_id',
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
                    'Us2.username AS project_note_author'
                ])
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
            ->leftJoin('groups', function ($join) {
                $join->on('groups.id', '=', 'memberships.membershipable_id');
            })
            ->leftJoin('teams', function ($join) {
                $join->on('teams.id', '=', 'memberships.membershipable_id');
            })
            ->leftJoin('users AS Us1', function ($join) {
                $join->on('Us1.id', '=', 'groups.owner');
            })
            ->leftJoin('users AS Us2', function ($join) {
                $join->on('Us2.id', '=', 'teams.owner');
            })
            ->select([
                'memberships.id as membership_id',
                'memberships.membershipable_type as membership_type',
                'memberships.role as membership_role',
                'memberships.is_admin as is_admin',
                'groups.id as group_id',
                'groups.name as group_name',
                'teams.name as team_name',
                'teams.id as team_id',
                'Us1.username as group_owner',
                'Us1.slug as group_slug',
                'Us2.username as team_owner',
                'Us2.slug as team_slug'
            ])->get();

        $memberships = [];
        $currentMembershipId = 0;
        $membershipCount = 0;

        foreach($rawMemberships as $rawMembership) {
            if ($rawMembership->membership_id !== $currentMembershipId) {
                if( $rawMembership->membership_type === 'App\\Models\\Group') {
                    $memberships[$membershipCount]['name'] = $rawMembership->group_name;
                    $memberships[$membershipCount]['owner'] = $rawMembership->group_owner;
                    $memberships[$membershipCount]['slug'] = $rawMembership->group_slug;
                    $memberships[$membershipCount]['membership_role'] = $rawMembership->membership_role;
                    $memberships[$membershipCount]['admin'] = $rawMembership->is_admin;
                    $memberships[$membershipCount]['type'] = 'Group';
                }

                if( $rawMembership->membership_type === 'App\\Models\\Team') {
                    $memberships[$membershipCount]['name'] = $rawMembership->team_name;
                    $memberships[$membershipCount]['owner'] = $rawMembership->team_owner;
                    $memberships[$membershipCount]['slug'] = $rawMembership->team_slug;
                    $memberships[$membershipCount]['membership_role'] = $rawMembership->membership_role;
                    $memberships[$membershipCount]['admin'] = $rawMembership->is_admin;
                    $memberships[$membershipCount]['type'] = 'Team';
                }
            }

            ++$membershipCount;
            $currentMembershipId = $rawMembership->membership_id;
        }
        
        return $memberships;
    }
}