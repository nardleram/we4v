<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliation extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function affiliations() : object
    {
        return $this->belongsToMany(Group::class, 'affiliations', 'requested_of', 'requested_by');
    }
}
