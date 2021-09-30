<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['membershipable_id', 'membershipable_type', 'user_id', 'group_id', 'role', 'confirmed'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public static function getPendingMemberships() : object
    {
        return (new static())
            ->where('confirmed', false)
            ->where('user_id', auth()->id())
            ->get(['membershipable_id', 'membershipable_type']);
    }
}
