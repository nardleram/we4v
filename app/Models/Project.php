<?php

namespace App\Models;

use Carbon\Carbon;
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
        'completed',
        'projectable_id',
        'projectable_type',
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

    public function getStartDateAttribute($date)
    {
        return Carbon::parse($date)->format('d M Y');
    }

    public function getEndDateAttribute($date)
    {
        return Carbon::parse($date)->format('d M Y');
    }

    public function projectable() : object
    {
        return $this->morphTo();
    }

    public function user() : object
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function group() : object
    {
        return $this->belongsTo(Group::class);
    }

    public function tasks() :  object
    {
        return $this->hasMany(Task::class);
    }

    public function notes() : object
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
