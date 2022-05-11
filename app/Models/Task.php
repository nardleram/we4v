<?php

namespace App\Models;

use stdClass;
use App\Traits\Uuids;
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

    public static function getTaskUsers($taskMembershipDetails) : object
    {
        if (auth()->id()) {
            $taskIds = [];
        
            foreach($taskMembershipDetails as $taskMember) {
                if(!in_array($taskMember->task_id, $taskIds)) {
                    array_push($taskIds, $taskMember->task_id);
                }
            }

            return Task::whereIn('tasks.id', $taskIds)
            ->leftJoin('memberships AS Me', function($join) {
                $join->on('Me.membershipable_id', '=', 'tasks.id');
            })
            ->leftJoin('users AS Us', function($join) {
                $join->on('Us.id', '=', 'Me.user_id')
                ->where('Us.id', '!=', auth()->id());
            })
            ->select(['Us.username AS username', 'Me.membershipable_id as task_id'])
            ->get();
        }

        return new stdClass();
    }
}
