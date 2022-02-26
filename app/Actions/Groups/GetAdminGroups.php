<?php

namespace App\Actions\Groups;

use App\Models\Group;
use App\Models\Membership;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetAdminGroups
{
    use SoftDeletes;

    private $compileGroupsArray;

    public function __construct(CompileGroupsArray $compileGroupsArray)
    {
        $this->compileGroupsArray = $compileGroupsArray;
    }
    
    public function handle($userId)
    {
        $rawGroupIds = Membership::where('is_admin', true)
        ->where('confirmed', true)
        ->where('user_id', $userId)
        ->where('membershipable_type', 'App\\Models\\Group')
        ->get('membershipable_id as group_id');

        $rawGroups = Group::whereIn('groups.id', $rawGroupIds)
        ->leftJoin('teams', function ($join) {
            $join->on('groups.id', '=', 'teams.group_id');
        })
        ->leftJoin('memberships', function ($join) {
            $join->on('groups.id', '=', 'memberships.membershipable_id')
            ->orOn('teams.id', '=', 'memberships.membershipable_id');
        })
        ->join('users', function ($join) {
            $join->on('memberships.user_id', '=', 'users.id');
        })
        ->join('images', function ($join) {
            $join->on('users.id', '=', 'imageable_id')
            ->where('images.imageable_type', 'App\\Models\\User')
            ->where('images.format', 'profile');
        })
        ->select([
            'groups.name as group_name',
            'groups.id as group_id',
            'groups.owner as group_owner',
            'groups.description as group_description',
            'groups.geog_area as geog_area',
            'teams.id as team_id',
            'teams.name as team_name',
            'teams.function as team_function',
            'memberships.user_id as member_user_id',
            'memberships.membershipable_type as membership_type',
            'memberships.role as role',
            'memberships.confirmed as confirmed',
            'memberships.is_admin as admin',
            'memberships.deleted_at as declined',
            'users.username as username',
            'users.id as user_id',
            'images.path as path' 
        ])
        ->groupBy([
            'groups.id',
            'teams.id',
            'memberships.is_admin',
            'memberships.membershipable_type',
            'memberships.user_id',
            'memberships.role',
            'memberships.confirmed',
            'memberships.deleted_at',
            'users.username',
            'users.id',
            'images.path'
        ])
        ->orderBy('groups.name')
        ->orderBy('teams.name')
        ->orderBy('memberships.is_admin', 'desc')
        ->orderBy('users.username')
        ->get();

        return $this->compileGroupsArray->compileGroups($rawGroups, true);
    }
}