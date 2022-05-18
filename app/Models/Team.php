<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['name', 'function', 'owner', 'group_id'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function user()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function memberships() : object
    {
        return $this->morphMany(Membership::class, 'membershipable');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function projects() : object
    {
        return $this->morphMany(Project::class, 'projectable');
    }

    public function tasks() : object
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public static function getTeams()
    {
        return  (new static())
            ->where('owner', auth()->id())
            ->get(['name', 'id', 'owner'])->toArray();
    }
}
