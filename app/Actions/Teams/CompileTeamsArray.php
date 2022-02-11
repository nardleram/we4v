<?php

namespace App\Actions\Teams;

class CompileTeamsArray
{
    public function compileTeams($rawTeams) : array
    {
        $teams = [];
        $loop = 0;
        $teamCount = 0;
        $currentTeamId = 0;
        $teamMemberCount = 0;
        $currentMemberUserId = 0;

        foreach($rawTeams as $rawTeam) {
            if ($rawTeam->team_id !== $currentTeamId) {
                if ($loop > 0) {
                    ++$teamCount;
                }

                $teams[$teamCount]['team_id'] = $rawTeam->team_id;
                $teams[$teamCount]['team_name'] = $rawTeam->team_name;
                $teams[$teamCount]['team_function'] = $rawTeam->team_function;
                $teams[$teamCount]['group_name'] = $rawTeam->group_name;
                $teams[$teamCount]['group_id'] = $rawTeam->group_id;
                $teams[$teamCount]['team_owner'] = $rawTeam->team_owner;
            }

            if ($rawTeam->member_user_id !== $currentMemberUserId) {
                $teams[$teamCount]['teamMembers'][$teamMemberCount]['username'] = $rawTeam->member_username;
                $teams[$teamCount]['teamMembers'][$teamMemberCount]['user_id'] = $rawTeam->member_user_id;
                $teams[$teamCount]['teamMembers'][$teamMemberCount]['role'] = $rawTeam->member_role;
                $teams[$teamCount]['teamMembers'][$teamMemberCount]['admin'] = $rawTeam->member_admin;
                $teams[$teamCount]['teamMembers'][$teamMemberCount]['confirmed'] = $rawTeam->member_confirmed;
                $teams[$teamCount]['teamMembers'][$teamMemberCount]['invited'] = true;
                $teams[$teamCount]['teamMembers'][$teamMemberCount]['path'] = $rawTeam->path ? $rawTeam->path : 'images/nobody.png';
                ++$teamMemberCount;
            }
            
            $currentTeamId = $rawTeam->team_id;
            $currentMemberUserId = $rawTeam->member_user_id;
        }
        
        return $teams;
    }
    
}