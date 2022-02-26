<?php

namespace App\Models;

use stdClass;
use Carbon\Carbon;
use App\Traits\Uuids;
use Illuminate\Support\Facades\DB;
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
        // Votes assigned to groups must also send invites to the associates in that group's teams.

        $ids = [];

        if (auth()->id()) {
            $memberships = Membership::where('confirmed', true)
                ->where('user_id', auth()->id())
                ->get(['membershipable_id', 'membershipable_type']);
            
            foreach($memberships as $membership) {
                array_push($ids, $membership->membershipable_id);

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
        if (auth()->id()) {
            return Membership::where('memberships.user_id', $userId)
                ->where('membershipable_type', 'App\Models\Task')
                ->leftJoin('tasks AS Ta', function($join) {
                    $join->on('Ta.id', '=', 'membershipable_id');
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
                $mytasks[$taskCount]['task_notes'][$taskNoteCount]['task_note_id'] = $task->task_note_id;
                $mytasks[$taskCount]['task_notes'][$taskNoteCount]['task_note_body'] = $task->task_note_body;
                $mytasks[$taskCount]['task_notes'][$taskNoteCount]['task_note_author'] = $task->task_note_author;
                $mytasks[$taskCount]['task_notes'][$taskNoteCount]['task_note_created_at'] = Carbon::parse($task->task_note_created_at)->format('d M y, H:i');
            }

            if ($task->project_id === $task->project_noteable_id && !$task->completed) {
                $mytasks[$taskCount]['project_notes'][$projectNoteCount]['project_note_id'] = $task->project_note_id;
                $mytasks[$taskCount]['project_notes'][$projectNoteCount]['project_note_body'] = $task->project_note_body;
                $mytasks[$taskCount]['project_notes'][$projectNoteCount]['project_note_author'] = $task->project_note_author;
                $mytasks[$taskCount]['project_notes'][$projectNoteCount]['project_note_created_at'] = Carbon::parse($task->project_note_created_at)->format('d M y, H:i');
            }

            foreach($taskUsernameDetails as $usernameDetails) {
                if ($task->task_id === $usernameDetails->task_id && !in_array($usernameDetails->username, $addedMembers) && $usernameDetails->username) {
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
        $rawMemberships = DB::select("SELECT Me.id as membership_id,
            Me.membershipable_type as membership_type,
            Me.role as membership_role,
            Me.is_admin as is_admin,
            Gr.id as group_id,
            Gr.name as group_name,
            Te.name as team_name,
            Te.id as team_id,
            Us1.username as group_owner,
            Us2.username as team_owner
        FROM memberships Me
        LEFT OUTER JOIN groups Gr
        ON Gr.id = Me.membershipable_id
        LEFT OUTER JOIN teams Te
        ON Te.id = Me.membershipable_id
        LEFT OUTER JOIN users Us1
        ON Gr.owner = Us1.id
        LEFT OUTER JOIN users Us2
        ON Te.owner = Us2.id
        WHERE Me.user_id = '$userId'
        AND Me.confirmed = true
        ");

        $memberships = [];
        $loop = 0;
        $currentMembershipId = 0;
        $membershipCount = 0;

        foreach($rawMemberships as $rawMembership) {
            if ($rawMembership->membership_id !== $currentMembershipId) {
                if ($loop > 0) {

                }

                if( $rawMembership->membership_type === 'App\\Models\\Group') {
                    $memberships[$membershipCount]['name'] = $rawMembership->group_name;
                    $memberships[$membershipCount]['owner'] = $rawMembership->group_owner;
                    $memberships[$membershipCount]['membership_role'] = $rawMembership->membership_role;
                    $memberships[$membershipCount]['admin'] = $rawMembership->is_admin;
                    $memberships[$membershipCount]['type'] = 'Group';
                }

                if( $rawMembership->membership_type === 'App\\Models\\Team') {
                    $memberships[$membershipCount]['name'] = $rawMembership->team_name;
                    $memberships[$membershipCount]['owner'] = $rawMembership->team_owner;
                    $memberships[$membershipCount]['membership_role'] = $rawMembership->membership_role;
                    $memberships[$membershipCount]['admin'] = $rawMembership->is_admin;
                    $memberships[$membershipCount]['type'] = 'Team';
                }
                
            }

            ++$loop;
            ++$membershipCount;
            $currentMembershipId = $rawMembership->membership_id;
        }
        
        return $memberships;
    }
}