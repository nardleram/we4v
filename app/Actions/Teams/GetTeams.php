<?php

namespace App\Actions\Teams;

use App\Models\Team;

class GetTeams
{
    public function handle($userId) : array
    {
        $rawTeams = Team::where('owner', $userId)
            ->leftJoin('groups', function ($join) {
                $join->on('teams.id', '=', 'groups.team_id');
            })
            ->leftJoin('memberships', function ($join) {
                $join->on('teams.id', '=', 'memberships.membershipable_id')
                ->orOn('groups.id', '=', 'memberships.membershipable_id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('memberships.user_id', '=', 'users.id');
            })
            ->leftJoin('images', function ($join) {
                $join->on('users.id', '=', 'imageable_id')
                ->where('images.imageable_type', 'App\Models\User')
                ->where('images.format', 'profile');
            })
            ->select([
                'teams.name as team_name',
                'teams.id as team_id',
                'teams.description as team_purpose',
                'groups.id as group_id',
                'groups.name as group_name',
                'groups.description as group_function',
                'memberships.user_id as user_id',
                'memberships.membershipable_type as membership_type',
                'memberships.role as role',
                'memberships.confirmed as confirmed',
                'users.id as user_id',
                'users.username as username',
                'images.path as user_profile_pic' 
            ])
            ->groupBy([
                'teams.id',
                'groups.id',
                'memberships.membershipable_type',
                'memberships.user_id',
                'memberships.role',
                'memberships.confirmed',
                'users.id',
                'images.path'
            ])
            ->orderBy('teams.name')
            ->orderBy('groups.name')
            ->orderBy('users.username')
            ->get();

        // Assemble $teams array
        $teams = [];
        $currentTeamId = 0;
        $loop = 0;
        $teamCount = 0;
        $groupCount = 0;
        $teamMemberCount = 0;
        $groupMemberCount = 0;

        foreach ($rawTeams as $rawTeam) {
            if (($currentTeamId !== $rawTeam->team_id) && $loop > 0) {
                ++$teamCount;
            }

            // Don't gather team data more than once
            if ($rawTeam->team_id !== $currentTeamId) {
                $teams[$teamCount]['team_name'] = $rawTeam->team_name;
                $teams[$teamCount]['team_purpose'] = $rawTeam->team_purpose;
                $teams[$teamCount]['team_id'] = $rawTeam->team_id;
            }

            if ($rawTeam->group_id) { // Got groups
                $teams[$teamCount]['groups'][$groupCount]['group_id'] = $rawTeam->group_id;
                $teams[$teamCount]['groups'][$groupCount]['group_name'] = $rawTeam->group_name;
                $teams[$teamCount]['groups'][$groupCount]['group_function'] = $rawTeam->group_function;
                ++$groupCount;
            }

            // Assign users (members) to correct teams and/or groups
            if ($rawTeam->user_id) {
                if ($rawTeam->membership_type === 'App\Models\Team') {
                    $teams[$teamCount]['teamMembers'][$teamMemberCount]['username'] = $rawTeam->username;
                    $teams[$teamCount]['teamMembers'][$teamMemberCount]['role'] = $rawTeam->role;
                    $teams[$teamCount]['teamMembers'][$teamMemberCount]['confirmed'] = $rawTeam->confirmed;
                    $teams[$teamCount]['teamMembers'][$teamMemberCount]['user_profile_pic'] = $rawTeam->user_profile_pic;
                    ++$teamMemberCount;
                }
                if ($rawTeam->membership_type === 'App\Models\Group') {
                    $teams[$teamCount]['groupMembers'][$groupMemberCount]['username'] = $rawTeam->username;
                    $teams[$teamCount]['groupMembers'][$groupMemberCount]['user_profile_pic'] = $rawTeam->user_profile_pic;
                    ++$groupMemberCount;
                }
            }

            $currentTeamId = $rawTeam->team_id;
            ++$loop;
        }
        
        return $teams;
    }
}