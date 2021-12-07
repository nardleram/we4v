<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastVote extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['element_id', 'user_id', 'vote_id'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
}
