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
        ->join('users AS Us1', function ($join) {
            $join->on('groups.owner', '=', 'Us1.id');
        })
        ->leftJoin('memberships', function ($join) {
            $join->on('groups.id', '=', 'memberships.membershipable_id')
            ->orOn('teams.id', '=', 'memberships.membershipable_id');
        })
        ->join('users AS Us2', function ($join) {
            $join->on('memberships.user_id', '=', 'Us2.id');
        })
        ->join('images', function ($join) {
            $join->on('Us2.id', '=', 'imageable_id')
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
            'memberships.id as membership_id',
            'memberships.user_id as member_user_id',
            'memberships.membershipable_type as membership_type',
            'memberships.role as role',
            'memberships.confirmed as confirmed',
            'memberships.is_admin as admin',
            'memberships.deleted_at as declined',
            'Us1.username as group_owner_username',
            'Us2.username as username',
            'Us2.id as user_id',
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
            'Us1.username',
            'Us2.username',
            'Us2.id',
            'memberships.id',
            'images.path'
        ])
        ->orderBy('groups.name')
        ->orderBy('teams.name')
        ->orderBy('memberships.is_admin', 'desc')
        ->orderBy('Us2.username')
        ->get();

        return $this->compileGroupsArray->compileGroups($rawGroups, true);
    }
}