<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Tag extends Model
{
    use Uuids;

    protected $fillable = ['name', 'tagable_id', 'tagable_type'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
}
