<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Network extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
}
