<?php

namespace App\Models;

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
        'group_id',
        'team_id',
        'user_id',
        'owner'
    ];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function project() : object
    {
        return $this->belongsTo(Project::class);
    }
}
