<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $fillable = [
        'start_date', 
        'end_date', 
        'description',
        'user_id',
        'owner',
        'taskable_id',
        'taskable_type',
        'name',
        'project_id'
    ];
    
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function project() : object
    {
        return $this->belongsTo(Project::class);
    }

    public static function getOpenTasks($ids) : array
    {
        $tasks = Task::whereIn('tasks.taskable_id', $ids)
        ->orWhere('tasks.user_id', auth()->id())
        ->leftJoin('notes', function ($join) {
            $join->on('notes.noteable_id', '=', 'tasks.id');
        })
        ->leftJoin('groups', function ($join) {
            $join->on('groups.id', '=', 'tasks.taskable_id');
        })
        ->leftJoin('teams', function ($join) {
            $join->on('teams.id', '=', 'tasks.taskable_id');
        })
        ->leftJoin('users', function ($join) {
            $join->on('tasks.owner', '=', 'users.id')
            ->orOn('notes.user_id', '=', 'users.id');
        })
        ->groupBy([
            'tasks.id',
            'notes.noteable_id',
            'notes.id',
            'groups.id',
            'teams.id',
            'users.id'
        ])
        ->orderBy('tasks.end_date', 'asc')
        ->orderBy('notes.created_at', 'asc')
        ->get([
            'tasks.id as task_id', 
            'tasks.name', 
            'tasks.taskable_id', 
            'tasks.taskable_type', 
            'tasks.description',
            'tasks.end_date',
            'tasks.completed',
            'tasks.owner',
            'tasks.user_id as task_user_id',
            'groups.name as group_name',
            'teams.name as team_name',
            'notes.body',
            'notes.created_at as note_created_at',
            'notes.id as note_id',
            'notes.noteable_id',
            'notes.user_id as note_user_id',
            'users.username',
            'users.id as user_id'
        ]);

        $mytasks = [];
        $loop = 0;
        $taskCount = 0;
        $noteCount = 0;
        $currentTaskId = 0;
        $currentNoteId = 0;

        foreach($tasks as $task) {
            if ($loop > 0) {
                if ($currentTaskId !== $task->task_id) {
                    ++$taskCount;
                }

                if ($currentNoteId !== $task->note_id) {
                    ++$noteCount;
                }
            }

            if (!$task->task_user_id || $task->task_user_id === auth()->id()) {
                if ($currentTaskId !== $task->task_id && !$task->completed) {
                    $mytasks[$taskCount]['name'] = $task->name;
                    $mytasks[$taskCount]['id'] = $task->task_id;
                    $mytasks[$taskCount]['end_date'] = Carbon::parse($task->end_date)->format('d M y');
                    $mytasks[$taskCount]['input_end_date'] = $task->end_date;
                    $mytasks[$taskCount]['completed'] = $task->completed;
                    $mytasks[$taskCount]['description'] = $task->description;
                    $mytasks[$taskCount]['owner'] = $task->username;
                    $mytasks[$taskCount]['taskable_id'] = $task->taskable_id;
                    $mytasks[$taskCount]['taskable_type'] = $task->taskable_type;
                    $mytasks[$taskCount]['task_user_id'] = $task->task_user_id;
                    $mytasks[$taskCount]['group_name'] = $task->group_name;
                    $mytasks[$taskCount]['team_name'] = $task->team_name;
                }
            }

            if ($task->task_id === $task->noteable_id && !$task->completed) {
                $mytasks[$taskCount]['notes'][$noteCount]['note_id'] = $task->note_id;
                $mytasks[$taskCount]['notes'][$noteCount]['note_body'] = $task->body;
                $mytasks[$taskCount]['notes'][$noteCount]['note_user_id'] = $task->note_user_id;
                $mytasks[$taskCount]['notes'][$noteCount]['note_created_at'] = Carbon::parse($task->note_created_at)->format('d M y, H:i');

                if ($task->user_id === $task->note_user_id) {
                    $mytasks[$taskCount]['notes'][$noteCount]['note_author'] = $task->username;
                }
            }

            $currentTaskId = $task->task_id;
            $currentNoteId = $task->note_id;
            ++$loop;
        }
        
        return $mytasks;
    }
}
