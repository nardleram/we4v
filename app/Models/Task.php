<?php

namespace App\Models;

use stdClass;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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
    
    public function taskable() : object
    {
        return $this->morphTo();
    }

    public function notes() : object
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function user() : object
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public static function getTaskUsers($taskMembershipDetails) : object
    {
        if (auth()->id()) {
            $userTaskIds = [];
            $teamTaskIds = [];
            $users = new Collection();
        
            foreach($taskMembershipDetails as $taskMember) {
                if ($taskMember->membership_user_id) { // Tasked assigned to user(s)
                    if (!in_array($taskMember->task_id, $userTaskIds)) {
                        array_push($userTaskIds, $taskMember->task_id);
                    }
                } else { // Get team members
                    if (!in_array($taskMember->task_id, $teamTaskIds)) {
                        array_push($teamTaskIds, $taskMember->task_id);
                    }
                }
            }


            $users = $users->merge(
                Task::whereIn('tasks.id', $userTaskIds)
                ->leftJoin('memberships AS Me', function($join) {
                    $join->on('Me.membershipable_id', '=', 'tasks.id');
                })
                ->join('users AS Us', function($join) {
                    $join->on('Us.id', '=', 'Me.user_id')
                    ->where('Us.id', '!=', auth()->id());
                })
                ->select(['Us.username AS username', 'tasks.id as task_id'])
                ->get()
            );

            $users = $users->merge(
                Task::whereIn('tasks.id', $teamTaskIds)
                ->join('teams AS Te', function($join) {
                    $join->on('Te.id', '=', 'tasks.taskable_id');
                })
                ->join('memberships AS Me', function($join) {
                    $join->on('Me.membershipable_id', '=', 'Te.id');
                })
                ->join('users AS Us', function($join) {
                    $join->on('Us.id', '=', 'Me.user_id')
                    ->where('Me.user_id', '!=', auth()->id());
                })
                ->select(['Us.username AS username', 'tasks.id as task_id'])
                ->get()
            );

            return $users;
        }

        return new stdClass();
    }
}
