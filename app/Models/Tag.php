<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['name', 'tagable_id', 'tagable_type'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
}
