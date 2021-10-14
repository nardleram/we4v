<?php

namespace App\Actions\Groups;

use App\Models\Group;
use Illuminate\Database\QueryException;

class GetGroups
{
    public function handle($userId)
    {
        try {
            $rawGroups = Group::where('groups.owner', $userId)
                ->leftJoin('teams', function ($join) {
                    $join->on('groups.id', '=', 'teams.group_id');
                })
                ->join('memberships', function ($join) {
                    $join->on('groups.id', '=', 'memberships.membershipable_id')
                    ->orOn('teams.id', '=', 'memberships.membershipable_id');
                })
                ->join('users', function ($join) {
                    $join->on('memberships.user_id', '=', 'users.id');
                })
                ->join('images', function ($join) {
                    $join->on('users.id', '=', 'imageable_id')
                    ->where('images.imageable_type', 'App\Models\User')
                    ->where('images.format', 'profile');
                })
                ->select([
                    'groups.name as group_name',
                    'groups.id as group_id',
                    'groups.description as group_description',
                    'groups.geog_area as geog_area',
                    'teams.id as team_id',
                    'teams.name as team_name',
                    'teams.function as team_function',
                    'memberships.user_id as user_id',
                    'memberships.group_id as member_group_id',
                    'memberships.membershipable_type as membership_type',
                    'memberships.role as role',
                    'memberships.confirmed as confirmed',
                    'memberships.is_admin as admin',
                    'users.username as username',
                    'images.path as user_profile_pic' 
                ])
                ->groupBy([
                    'groups.id',
                    'teams.id',
                    'memberships.is_admin',
                    'memberships.membershipable_type',
                    'memberships.user_id',
                    'memberships.group_id',
                    'memberships.role',
                    'memberships.confirmed',
                    'users.username',
                    'images.path'
                ])
                ->orderBy('groups.name')
                ->orderBy('teams.name')
                ->orderBy('memberships.is_admin', 'desc')
                ->orderBy('users.username')
                ->get();
        } catch (QueryException $exception) {
            return view('groups.queryException');
        } catch (\Exception $exception) {
            return view('groups.queryException');
        }

        // Assemble $groups array
        $groups = [];
        $currentGroupId = 0;
        $currentTeamId = 0;
        $loop = 0;
        $teamCount = 0;
        $groupCount = 0;
        $teamMemberCount = 0;
        $groupMemberCount = 0;

        foreach ($rawGroups as $rawGroup) {
            if (($currentGroupId !== $rawGroup->group_id) && $loop > 0) {
                ++$groupCount;
                $teamCount = 0;
            }
            if ($rawGroup->team_id && ($currentTeamId !== $rawGroup->team_id) && $loop > 0) {
                ++$teamCount;
            }

            // Don't gather team data more than once
            if ($rawGroup->team_id !== $currentGroupId) {
                $groups[$groupCount]['group_name'] = $rawGroup->group_name;
                $groups[$groupCount]['group_description'] = $rawGroup->group_description;
                $groups[$groupCount]['group_id'] = $rawGroup->group_id;
                $groups[$groupCount]['geog_area'] = $rawGroup->geog_area;
            }

            if ($rawGroup->team_id) { // Got teams
                $groups[$groupCount]['teams'][$teamCount]['team_id'] = $rawGroup->team_id;
                $groups[$groupCount]['teams'][$teamCount]['team_name'] = $rawGroup->team_name;
                $groups[$groupCount]['teams'][$teamCount]['team_function'] = $rawGroup->team_function;
            }

            // Assign users and groups (members) to correct teams and/or groups
            if ($rawGroup->user_id || $rawGroup->member_group_id) {
                if ($rawGroup->membership_type === 'App\Models\Group' && $rawGroup->user_id && $teamCount < 1) {
                    $groups[$groupCount]['groupMembers'][$groupMemberCount]['username'] = $rawGroup->username;
                    $groups[$groupCount]['groupMembers'][$groupMemberCount]['role'] = $rawGroup->role;
                    $groups[$groupCount]['groupMembers'][$groupMemberCount]['confirmed'] = $rawGroup->confirmed;
                    $groups[$groupCount]['groupMembers'][$groupMemberCount]['user_profile_pic'] = $rawGroup->user_profile_pic;
                    $groups[$groupCount]['groupMembers'][$groupMemberCount]['admin'] = $rawGroup->admin;
                    ++$groupMemberCount;
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
                    ++$teamMemberCount;
                }
            }

            $currentGroupId = $rawGroup->group_id;
            $currentTeamId = $rawGroup->team_id;
            ++$loop;
        }
        
        return $groups;
    }
}