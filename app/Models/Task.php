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

    public function project() : object
    {
        return $this->belongsTo(Project::class);
    }
}
