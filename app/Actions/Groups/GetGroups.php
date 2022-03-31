<?php

namespace App\Actions\Groups;

use App\Models\Group;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetGroups
{
    use SoftDeletes;

    private $compileGroupsArray;

    public function __construct(CompileGroupsArray $compileGroupsArray)
    {
        $this->compileGroupsArray = $compileGroupsArray;
    }
    
    public function handle($userId)
    {
        $rawGroups = Group::where('groups.owner', $userId)
        ->leftJoin('teams', function ($join) {
            $join->on('groups.id', '=', 'teams.group_id');
        })
        ->leftJoin('memberships', function ($join) {
            $join->on('groups.id', '=', 'memberships.membershipable_id')
            ->orOn('teams.id', '=', 'memberships.membershipable_id');
        })
        ->leftJoin('users', function ($join) {
            $join->on('memberships.user_id', '=', 'users.id');
        })
        ->leftJoin('users AS Us2', function ($join) {
            $join->on('memberships.updated_by', '=', 'Us2.id');
        })
        ->leftJoin('images', function ($join) {
            $join->on('users.id', '=', 'imageable_id')
            ->where('images.imageable_type', 'App\Models\User')
            ->where('images.format', 'profile');
        })
        ->select([
            'groups.name as group_name',
            'groups.id as group_id',
            'groups.description as group_description',
            'groups.owner as group_owner',
            'groups.geog_area as geog_area',
            'groups.created_at as group_created_at',
            'teams.id as team_id',
            'teams.name as team_name',
            'teams.function as team_function',
            'teams.created_at as team_created_at',
            'memberships.id as membership_id',
            'memberships.user_id as member_user_id',
            'memberships.membershipable_type as membership_type',
            'memberships.role as role',
            'memberships.confirmed as confirmed',
            'memberships.is_admin as admin',
            'memberships.deleted_at as declined',
            'memberships.created_at as created_at',
            'users.username as username',
            'Us2.username as updated_by',
            'users.id as user_id',
            'images.path as path' 
        ])
        ->groupBy([
            'groups.id',
            'teams.id',
            'memberships.membershipable_type',
            'memberships.id',
            'memberships.is_admin',
            'memberships.user_id',
            'memberships.role',
            'memberships.confirmed',
            'memberships.deleted_at',
            'memberships.created_at',
            'groups.owner',
            'users.username',
            'users.id',
            'Us2.username',
            'images.path'
        ])
        ->orderBy('groups.name')
        ->orderBy('teams.name')
        ->orderBy('memberships.membershipable_type')
        ->orderBy('memberships.is_admin', 'desc')
        ->orderBy('users.username')
        ->get();

        return $this->compileGroupsArray->compileGroups($rawGroups, false);
    }
}