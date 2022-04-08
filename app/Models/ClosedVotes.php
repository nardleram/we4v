<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClosedVotes extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $guarded = ['id'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    protected $casts = [
        'usernames' => 'array',
        'elements' => 'array'
    ];
}
