<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\Membership;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetAdminTeams
{
    use SoftDeletes;

    private $compileTeamsArray;

    public function __construct(CompileTeamsArray $compileTeamsArray)
    {
        $this->compileTeamsArray = $compileTeamsArray;
    }
    
    public function handle($userId) : array
    {
        $rawTeamIds = Membership::where('is_admin', true)
        ->where('confirmed', true)
        ->where('user_id', $userId)
        ->where('membershipable_type', 'App\\Models\\Team')
        ->get('membershipable_id as team_id');

        $rawTeams = Team::whereIn('teams.id', $rawTeamIds)
        ->leftJoin('groups', function ($join) {
            $join->on('groups.id', '=', 'teams.group_id');
        })
        ->leftJoin('memberships', function ($join) {
            $join->on('memberships.membershipable_id', '=', 'teams.id')
            ->where('memberships.deleted_at', null);
        })
        ->leftJoin('users AS Us1', function ($join) {
            $join->on('Us1.id', '=', 'teams.owner');
        })
        ->leftJoin('users AS Us2', function ($join) {
            $join->on('Us2.id', '=', 'memberships.user_id');
        })
        ->leftJoin('images AS Im', function ($join) {
            $join->on('Im.imageable_id', '=', 'Us2.id')
            ->where('Im.format', 'profile');
        })
        ->select([
            'teams.id as team_id',
            'teams.name as team_name',
            'teams.function as team_function',
            'groups.name as group_name',
            'groups.id as group_id',
            'memberships.role as member_role',
            'memberships.confirmed as member_confirmed',
            'memberships.is_admin as member_admin',
            'memberships.updated_by as member_updated_by',
            'Us1.username as team_owner',
            'Us2.username as member_username',
            'Us2.id as member_user_id',
            'Im.path as path'
        ])
        ->get();
        
        return $this->compileTeamsArray->compileTeams($rawTeams);
    }
}