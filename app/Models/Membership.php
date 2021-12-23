<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Actions\Memberships\CompileUserMemberships;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public static function getUserMemberships($userId) : array
    {
        $rawMemberships = DB::select("SELECT Me.id as membership_id,
            Me.membershipable_type as membership_type,
            Me.role as membership_role,
            Me.is_admin as is_admin,
            Gr.id as group_id,
            Gr.name as group_name,
            Te.name as team_name,
            Te.id as team_id,
            Us1.username as group_owner,
            Us2.username as team_owner
        FROM memberships Me
        LEFT OUTER JOIN groups Gr
        ON Gr.id = Me.membershipable_id
        LEFT OUTER JOIN teams Te
        ON Te.id = Me.membershipable_id
        LEFT OUTER JOIN users Us1
        ON Gr.owner = Us1.id
        LEFT OUTER JOIN users Us2
        ON Te.owner = Us2.id
        WHERE Me.user_id = '$userId'
        AND Me.confirmed = true
        ");

        $memberships = [];
        $loop = 0;
        $currentMembershipId = 0;
        $membershipCount = 0;

        foreach($rawMemberships as $rawMembership) {
            if ($rawMembership->membership_id !== $currentMembershipId) {
                if ($loop > 0) {

                }

                if( $rawMembership->membership_type === 'App\\Models\\Group') {
                    $memberships[$membershipCount]['name'] = $rawMembership->group_name;
                    $memberships[$membershipCount]['owner'] = $rawMembership->group_owner;
                    $memberships[$membershipCount]['membership_role'] = $rawMembership->membership_role;
                    $memberships[$membershipCount]['admin'] = $rawMembership->is_admin;
                    $memberships[$membershipCount]['type'] = 'Group';
                }

                if( $rawMembership->membership_type === 'App\\Models\\Team') {
                    $memberships[$membershipCount]['name'] = $rawMembership->team_name;
                    $memberships[$membershipCount]['owner'] = $rawMembership->team_owner;
                    $memberships[$membershipCount]['membership_role'] = $rawMembership->membership_role;
                    $memberships[$membershipCount]['admin'] = $rawMembership->is_admin;
                    $memberships[$membershipCount]['type'] = 'Team';
                }
                
            }

            ++$loop;
            ++$membershipCount;
            $currentMembershipId = $rawMembership->membership_id;
        }
        
        return $memberships;
    }
}