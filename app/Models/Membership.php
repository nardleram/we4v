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

    protected $fillable = [
        'membershipable_id', 
        'membershipable_type', 
        'user_id', 
        'group_id', 
        'role', 
        'confirmed', 
        'is_admin'
    ];
    protected $casts = ['is_admin' => 'boolean'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public static function getPendingMemberships() : object
    {
        return (new static())
            ->where('confirmed', false)
            ->where('user_id', auth()->id())
            ->get(['membershipable_id', 'membershipable_type', 'role']);
    }

    public static function getMemberships() : object
    {
        return (new static())
            ->where('confirmed', true)
            ->where('user_id', auth()->id())
            ->get(['membershipable_id']);
    }

    public static function getMemberships4Votes() : array
    {
        // Votes assigned to groups must also send invites to the associates in that group's teams.

        $ids = [];

        $memberships = Membership::where('confirmed', true)
            ->where('user_id', auth()->id())
            ->get(['membershipable_id', 'membershipable_type']);
        
        foreach($memberships as $membership) {
            array_push($ids, $membership->membershipable_id);

            if ($membership->membershipable_type === 'App\Models\Team') {
                $team = Team::findOrFail($membership->membershipable_id);

                foreach($team->group()->get('id') as $groupId) {
                    !in_array($groupId->id, $ids) 
                    ? array_push($ids, $groupId->id)
                    : null;
                }
            } 
        }
        
        return $ids;
    }

    public static function getMemberships4Tasks() : array
    {
        // Tasks assigned to teams are assigned to all members of that team.

        $ids = [];

        $memberships = Membership::where('confirmed', true)
            ->where('user_id', auth()->id())
            ->where('membershipable_type', 'App\Models\Team')
            ->get('membershipable_id');
        
        foreach($memberships as $membership) {
            !in_array($membership->membershipable_id, $ids) 
            ? array_push($ids, $membership->membershipable_id)
            : null;
        }
        
        return $ids;
    }
}
