<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Group extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = ['name', 'owner', 'description'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function memberships() : object
    {
        return $this->hasMany(Membership::class);
    }
}
