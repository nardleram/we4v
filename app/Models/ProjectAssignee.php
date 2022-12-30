<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class ProjectAssignee extends Model
{
    use Uuids;

    protected $fillable = ['project_id', 'assignee_id', 'assignee_type'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
}
