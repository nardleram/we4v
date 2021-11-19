<?php

namespace App\Actions\Projects;

use Carbon\Carbon;
use App\Models\Project;
use DateTime;

class GetProjects
{
    public function handle($userId) : array
    {
        $rawProjects = Project::where('projects.owner', $userId)
        ->leftJoin('tasks', function ($join) use ($userId) {
            $join->on('tasks.project_id', '=', 'projects.id')
            ->where('tasks.owner', $userId);
        })
        ->leftJoin('groups', function ($join) use ($userId) {
            $join->on('projects.group_id', '=', 'groups.id')
            ->where('groups.owner', $userId);
        })
        ->leftJoin('teams', function ($join) use ($userId) {
            $join->on('groups.id', '=', 'teams.group_id')
            ->where('teams.owner', $userId);
        })
        ->leftJoin('memberships', function ($join) {
            $join->on('groups.id', '=', 'membershipable_id')
            ->orOn('teams.id', '=', 'membershipable_id');
        })
        ->leftJoin('users', function ($join) {
            $join->on('memberships.user_id', '=', 'users.id');
        })
        ->select([
            'projects.id as project_id',
            'projects.name as project_name',
            'projects.description as project_description',
            'projects.start_date as project_start_date',
            'projects.end_date as project_end_date',
            'projects.group_id as project_group_id',
            'tasks.id as task_id',
            'tasks.name as task_name',
            'tasks.description as task_description',
            'tasks.start_date as task_start_date',
            'tasks.end_date as task_end_date',
            'tasks.taskable_type as taskable_type',
            'tasks.taskable_id as taskable_id',
            'tasks.user_id as task_user_id',
            'tasks.project_id as task_project_id',
            'groups.name as project_group_name',
            'memberships.user_id as member_user_id',
            'memberships.membershipable_type as member_type',
            'memberships.membershipable_id as member_id',
            'memberships.confirmed as member_confirmed',
            'teams.id as team_id',
            'teams.name as team_name',
            'users.username as username',
            // Profile pics?
        ])
        ->groupBy([
            'projects.id',
            'tasks.id',
            'groups.id',
            'teams.id',
            'memberships.is_admin',
            'memberships.membershipable_type',
            'memberships.membershipable_id',
            'memberships.user_id',
            'memberships.group_id',
            'memberships.role',
            'memberships.confirmed',
            'tasks.taskable_id',
            'users.username',
        ])
        ->orderBy('projects.end_date', 'DESC')
        ->orderBy('tasks.end_date', 'DESC')
        ->orderBy('groups.name')
        ->orderBy('teams.name')
        ->orderby('tasks.end_date')
        ->orderBy('memberships.is_admin', 'desc')
        ->orderBy('users.username')
        ->get();
        
        // Assemble $projects array
        $currentProjectId = 0;
        $currentTaskId = 0;
        $projectCount = 0;
        $taskCount = 0;
        $taskTeamMemberCount = 0;
        $projects = [];
        $loop = 0;
        $today = date("Y-m-d");
        $todayDate = new DateTime($today);
        $deadlinePassed = false;

        foreach($rawProjects as $rawProject) {
            if ($currentProjectId !== $rawProject->project_id && $loop > 0) {
                ++$projectCount;
            }

            if ($currentTaskId !== $rawProject->task_id && $loop > 0) {
                ++$taskCount;
                $taskTeamMemberCount = 0;
            }

            // Compile project data once only
            if ($currentProjectId !== $rawProject->project_id) {
                $toDate = new DateTime($rawProject->project_end_date);
                $interval = $todayDate->diff($toDate);
                $projectDays = $interval->format('%a');
                if ($todayDate > $toDate) {
                    $deadlinePassed = true;
                }
                $projects[$projectCount]['project_name'] = $rawProject->project_name;
                $projects[$projectCount]['project_description'] = $rawProject->project_description;
                $projects[$projectCount]['project_id'] = $rawProject->project_id;
                $projects[$projectCount]['project_group_id'] = $rawProject->project_group_id;
                $projects[$projectCount]['project_start_date'] = Carbon::parse($rawProject->project_start_date)->format('d M Y');
                $projects[$projectCount]['project_end_date'] = Carbon::parse($rawProject->project_end_date)->format('d M Y');
                $projects[$projectCount]['project_group_name'] = $rawProject->project_group_name;
                $projects[$projectCount]['project_deadline_passed'] = $deadlinePassed;
                $deadlinePassed 
                ? $projects[$projectCount]['project_days_remaining'] = '-'.$projectDays
                : $projects[$projectCount]['project_days_remaining'] = $projectDays;
            }

            // Task assigned to group member
            if ($rawProject->taskable_type === 'App\\Models\\Group' && $rawProject->member_type === 'App\\Models\\Group' && $rawProject->member_confirmed && $rawProject->member_user_id === $rawProject->task_user_id) {
                $projects[$projectCount]['tasks'][$taskCount]['group_name'] = $rawProject->project_group_name;
                $projects[$projectCount]['tasks'][$taskCount]['group_member_username'] = $rawProject->username;
                $projects[$projectCount]['tasks'][$taskCount]['task_name'] = $rawProject->task_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_description'] = $rawProject->task_description;
                $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M Y');
                $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M Y');
            }

            // Task assigned to whole team?
            if ($rawProject->taskable_type === 'App\\Models\\Team' && !$rawProject->task_user_id) {
                $projects[$projectCount]['tasks'][$taskCount]['task_name'] = $rawProject->task_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_description'] = $rawProject->task_description;
                $projects[$projectCount]['tasks'][$taskCount]['team_id'] = $rawProject->team_id;
                $projects[$projectCount]['tasks'][$taskCount]['team_name'] = $rawProject->team_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M Y');
                $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M Y');
                // Compile team users
                if ($rawProject->member_type === 'App\\Models\\Team') {
                    $projects[$projectCount]['tasks'][$taskCount]['team_members'][$taskTeamMemberCount]['team_member_username'] = $rawProject->username;
                    // Profile pic?
                    ++$taskTeamMemberCount;
                }
                
            }

            // Task assigned to specific team member
            if ($rawProject->taskable_type === 'App\\Models\\Team' && $rawProject->task_user_id) {
                $projects[$projectCount]['tasks'][$taskCount]['task_name'] = $rawProject->task_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_description'] = $rawProject->task_description;
                $projects[$projectCount]['tasks'][$taskCount]['team_id'] = $rawProject->team_id;
                $projects[$projectCount]['tasks'][$taskCount]['team_name'] = $rawProject->team_name;
                $projects[$projectCount]['tasks'][$taskCount]['team_member_username'] = $rawProject->username;
                $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M Y');
                $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M Y');
            }

            $currentProjectId = $rawProject->project_id;
            $currentTaskId = $rawProject->task_id;
            ++$loop;
        }
        
        return $projects;
    }
}
