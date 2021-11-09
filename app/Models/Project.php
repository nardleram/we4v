<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Project extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $fillable = [
        'name', 
        'owner', 
        'description', 
        'group_id', 
        'team_id', 
        'start_date', 
        'end_date'
    ];
    protected $casts = [
        'start_date' => 'datetime:d M Y',
        'end_date' => 'datetime:d M Y',
    ];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function user() : object
    {
        return $this->belongsTo(User::class);
    }

    public function tasks() :  object
    {
        return $this->hasMany(Task::class);
    }
}
