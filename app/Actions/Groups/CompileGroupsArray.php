<?php

namespace App\Actions\Groups;

class CompileGroupsArray
{
    public function compileGroups($rawGroups, $is_admin) : array 
    {
        // Assemble $groups array
        $groups = [];
        $groupMembers = [];
        $currentGroupId = 0;
        $currentTeamId = 0;
        $loop = 0;
        $teamCount = 0;
        $groupCount = 0;
        $teamMemberCount = 0;
        $groupMemberCount = 0;

        foreach ($rawGroups as $rawGroup) {
            if (($currentGroupId !== $rawGroup->group_id) && $loop > 0) {
                $groupMembers = [];
                ++$groupCount;
                $teamCount = 0;
                $groupMemberCount = 0;
                $teamMemberCount = 0;
                if ($rawGroup->team_id) {
                    $currentTeamId = $rawGroup->team_id;
                }
            }

            if ($rawGroup->membership_type === 'App\Models\Team' && ($currentTeamId !== $rawGroup->team_id) && $loop > 0) {
                ++$teamCount;
                $teamMemberCount = 0;
            }

            // Don't gather group data more than once
            if ($rawGroup->group_id !== $currentGroupId) {
                $groups[$groupCount]['group_name'] = $rawGroup->group_name;
                $groups[$groupCount]['group_description'] = $rawGroup->group_description;
                $groups[$groupCount]['group_id'] = $rawGroup->group_id;
                $groups[$groupCount]['geog_area'] = $rawGroup->geog_area;
                $groups[$groupCount]['am_admin'] = $is_admin;

                $rawGroup->group_owner
                ? $groups[$groupCount]['group_owner'] = $rawGroup->group_owner
                : null;
            }

            if ($rawGroup->team_id && $rawGroup->membership_type === 'App\Models\Team') { // Got a team
                $groups[$groupCount]['teams'][$teamCount]['team_id'] = $rawGroup->team_id;
                $groups[$groupCount]['teams'][$teamCount]['team_name'] = $rawGroup->team_name;
                $groups[$groupCount]['teams'][$teamCount]['team_function'] = $rawGroup->team_function;
                $groups[$groupCount]['teams'][$teamCount]['group_id'] = $rawGroup->group_id;
                $groups[$groupCount]['teams'][$teamCount]['currentTeamId'] = $currentTeamId;
            }

            // Assign users and groups (members) to correct teams and/or groups
            if ($rawGroup->user_id || $rawGroup->member_group_id) {
                if ($rawGroup->membership_type === 'App\Models\Group' && $rawGroup->member_user_id === $rawGroup->user_id) {
                    if (!in_array($rawGroup->member_user_id, $groupMembers)) {
                        array_push($groupMembers, $rawGroup->member_user_id);
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['username'] = $rawGroup->username;
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['role'] = $rawGroup->role;
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['confirmed'] = $rawGroup->confirmed;
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['path'] = $rawGroup->path ? $rawGroup->path : 'images/nobody.png';
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['admin'] = $rawGroup->admin;
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['declined'] = $rawGroup->declined;
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['user_id'] = $rawGroup->user_id;
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['updated_by'] = $rawGroup->updated_by;
                        $groups[$groupCount]['groupMembers'][$groupMemberCount]['invited'] = true;
                        ++$groupMemberCount;
                    }
                }

                if ($rawGroup->membership_type === 'App\Models\Group' && $rawGroup->member_group_id) {
                    $groups[$groupCount]['groupMembers'][$groupMemberCount]['groupname'] = $rawGroup->group_name;
                    $groups[$groupCount]['groupMembers'][$groupMemberCount]['description'] = $rawGroup->description;
                    $groups[$groupCount]['groupMembers'][$groupMemberCount]['confirmed'] = $rawGroup->confirmed;
                    ++$groupMemberCount;
                }

                if ($rawGroup->membership_type === 'App\Models\Team') {
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['username'] = $rawGroup->username;
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['role'] = $rawGroup->role;
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['confirmed'] = $rawGroup->confirmed;
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['admin'] = $rawGroup->admin;
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['declined'] = $rawGroup->declined;
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['user_id'] = $rawGroup->user_id;
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['updated_by'] = $rawGroup->updated_by;
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['path'] = $rawGroup->path ? $rawGroup->path : 'images/nobody.png';
                    $groups[$groupCount]['teams'][$teamCount]['teamMembers'][$teamMemberCount]['invited'] = true;
                    ++$teamMemberCount;
                }
            }

            $currentGroupId = $rawGroup->group_id;

            if ($loop === 0) {
                $currentTeamId = $rawGroup->team_id;
            }
            
            if ($rawGroup->team_id && $rawGroup->membershipable_type === 'App\\Models\\Team' && $loop > 0) {
                $currentTeamId = $rawGroup->team_id;
            }

            ++$loop;
        }
        
        return $groups;
    }
}