<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['body', 'user_id', 'noteable_id', 'noteable_type'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function user() : object
    {
        return $this->belongsTo(User::class);
    }
}
