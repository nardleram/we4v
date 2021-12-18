<?php

namespace App\Actions\Projects;

use Carbon\Carbon;
use App\Models\Project;
use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetProjects
{
    use SoftDeletes;
    
    public function handle($userId) : array
    {
        $rawProjects = Project::where('projects.owner', $userId)
        ->leftJoin('tasks', function ($join) {
            $join->on('tasks.project_id', '=', 'projects.id');
        })
        ->leftJoin('notes', function ($join) {
            $join->on('tasks.id', '=', 'notes.noteable_id')
            ->orOn('projects.id', '=', 'notes.noteable_id');
        })
        ->leftJoin('groups', function ($join) {
            $join->on('projects.group_id', '=', 'groups.id');
        })
        ->leftJoin('teams', function ($join) {
            $join->on('groups.id', '=', 'teams.group_id');
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
            'projects.completed as project_completed',
            'tasks.id as task_id',
            'tasks.completed as task_completed',
            'tasks.name as task_name',
            'tasks.description as task_description',
            'tasks.start_date as task_start_date',
            'tasks.end_date as task_end_date',
            'tasks.taskable_type as taskable_type',
            'tasks.taskable_id as taskable_id',
            'tasks.user_id as task_user_id',
            'tasks.project_id as task_project_id',
            'notes.id as note_id',
            'notes.body as note_body',
            'notes.noteable_type as noteable_type',
            'notes.user_id as note_user_id',
            'notes.created_at as note_created_at',
            'groups.name as project_group_name',
            'memberships.user_id as member_user_id',
            'memberships.membershipable_type as member_type',
            'memberships.membershipable_id as member_id',
            'memberships.confirmed as member_confirmed',
            'teams.id as team_id',
            'teams.name as team_name',
            'users.username as username',
            'users.id as user_id',
            // Profile pics?
        ])
        ->groupBy([
            'projects.id',
            'tasks.id',
            'groups.id',
            'teams.id',
            'notes.id',
            'memberships.is_admin',
            'memberships.membershipable_type',
            'memberships.membershipable_id',
            'memberships.user_id',
            'memberships.group_id',
            'memberships.confirmed',
            'users.username',
            'users.id'
        ])
        ->orderBy('projects.end_date', 'asc')
        ->orderBy('notes.created_at', 'asc')
        ->orderBy('tasks.end_date', 'asc')
        ->orderBy('projects.completed', 'asc')
        ->orderBy('tasks.completed', 'asc')
        ->orderBy('noteable_type', 'asc')
        ->orderBy('groups.name')
        ->orderBy('teams.name')
        ->orderBy('memberships.is_admin', 'desc')
        ->orderBy('users.username')
        ->get();
        
        // Assemble $projects array
        $currentProjectId = 0;
        $projectCount = 0;
        $taskCount = 0;
        $projectNoteCount = 0;
        $taskNoteCount = 0;
        $taskTeamMemberCount = 0;
        $projects = [];
        $notes = [];
        $tasks = [];
        $groupMembers = [];
        $teamMembers = [];
        $loop = 0;
        $today = date("Y-m-d");
        $todayDate = new DateTime($today);
        $deadlinePassed = false;
        $taskDeadlinePassed = false;

        foreach($rawProjects as $rawProject) {
            if (!in_array($rawProject->note_id, $notes)) {
                array_push($notes, $rawProject->note_id);
            }
            if (!in_array($rawProject->task_id, $tasks)) {
                array_push($tasks, $rawProject->task_id);
            }
            if (!in_array($rawProject->member_user_id, $groupMembers) && $rawProject->member_type = 'App\\Models\\Group') {
                array_push($groupMembers, $rawProject->member_user_id);
            }
            if (!in_array($rawProject->member_user_id, $teamMembers) && $rawProject->member_type = 'App\\Models\\Team') {
                array_push($teamMembers, $rawProject->member_user_id);
            }
        }
        
        foreach($rawProjects as $rawProject) {
            // Compile project data once only
            if ($currentProjectId !== $rawProject->project_id) {
                if ($loop > 0) {
                    ++$projectCount;
                    $deadlinePassed = false;
                    $taskCount = 0;
                }
                $toDate = new DateTime($rawProject->project_end_date);
                $interval = $todayDate->diff($toDate);
                $projectDays = $interval->format('%a');
                if ($todayDate > $toDate) {
                    $deadlinePassed = true;
                }

                $projects[$projectCount]['project_name'] = $rawProject->project_name;
                $projects[$projectCount]['project_completed'] = $rawProject->project_completed;
                $projects[$projectCount]['project_description'] = $rawProject->project_description;
                $projects[$projectCount]['project_id'] = $rawProject->project_id;
                $projects[$projectCount]['project_group_id'] = $rawProject->project_group_id;
                $projects[$projectCount]['project_start_date'] = Carbon::parse($rawProject->project_start_date)->format('d M Y');
                $projects[$projectCount]['project_end_date'] = Carbon::parse($rawProject->project_end_date)->format('d M Y');
                $projects[$projectCount]['project_input_end_date'] = $rawProject->project_end_date;
                $projects[$projectCount]['project_group_name'] = $rawProject->project_group_name;
                $projects[$projectCount]['project_deadline_passed'] = $deadlinePassed;
                $deadlinePassed 
                ? $projects[$projectCount]['project_days_remaining'] = '-'.$projectDays
                : $projects[$projectCount]['project_days_remaining'] = $projectDays;
            }

            // Project has notes
            if (($rawProject->noteable_type === 'App\\Models\\Project') && (in_array($rawProject->note_id, $notes))) {
                if (($key = array_search($rawProject->note_id, $notes)) !== false) {
                    unset($notes[$key]);
                }
                $projects[$projectCount]['notes'][$projectNoteCount]['note_id'] = $rawProject->note_id;
                $projects[$projectCount]['notes'][$projectNoteCount]['note_body'] = $rawProject->note_body;
                $projects[$projectCount]['notes'][$projectNoteCount]['note_created_at'] = Carbon::parse($rawProject->note_created_at)->format('d M y, H:i');
                $projects[$projectCount]['notes'][$projectNoteCount]['note_author'] = $rawProject->note_user_id;

                ++$projectNoteCount;
            }

            // TASKS
            $taskDeadlinePassed = false;
            $taskToDate = new DateTime($rawProject->task_end_date);
            $taskInterval = $todayDate->diff($taskToDate);
            $taskDays = $taskInterval->format('%a');
            if ($todayDate > $taskToDate) {
                $taskDeadlinePassed = true;
            }
            // Task assigned to group member
            if ( ($rawProject->taskable_type === 'App\\Models\\Group') && 
                ($rawProject->member_type === 'App\\Models\\Group') && 
                ($rawProject->member_confirmed) && 
                ($rawProject->member_user_id === $rawProject->task_user_id) &&
                (in_array($rawProject->task_id, $tasks))
            ) {

                if (($key = array_search($rawProject->task_id, $tasks)) !== false) {
                    unset($tasks[$key]);
                }

                $projects[$projectCount]['tasks'][$taskCount]['project_group_id'] = $rawProject->project_group_id;
                $projects[$projectCount]['tasks'][$taskCount]['task_input_end_date'] = $rawProject->task_end_date;
                $projects[$projectCount]['tasks'][$taskCount]['taskable_id'] = $rawProject->taskable_id;
                $projects[$projectCount]['tasks'][$taskCount]['taskable_type'] = $rawProject->taskable_type;
                $projects[$projectCount]['tasks'][$taskCount]['group_name'] = $rawProject->project_group_name;
                $projects[$projectCount]['tasks'][$taskCount]['recipient_type'] = 'group member';
                $projects[$projectCount]['tasks'][$taskCount]['assignee'] = $rawProject->username;
                $projects[$projectCount]['tasks'][$taskCount]['task_completed'] = $rawProject->task_completed;
                $projects[$projectCount]['tasks'][$taskCount]['task_deadline_passed'] = $taskDeadlinePassed;
                $projects[$projectCount]['tasks'][$taskCount]['task_description'] = $rawProject->task_description;
                $projects[$projectCount]['tasks'][$taskCount]['task_id'] = $rawProject->task_id;
                $projects[$projectCount]['tasks'][$taskCount]['task_user_id'] = $rawProject->task_user_id;
                $projects[$projectCount]['tasks'][$taskCount]['task_name'] = $rawProject->task_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_project_id'] = $rawProject->task_project_id;
                $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M y');
                $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M y');

                $taskDeadlinePassed
                ? $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = '-'.$taskDays
                : $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = $taskDays;
            }

            // Task assigned to whole team
            if ( ($rawProject->taskable_type === 'App\\Models\\Team') && 
                (!$rawProject->task_user_id) &&
                (in_array($rawProject->task_id, $tasks))
            ) {

                if (($key = array_search($rawProject->task_id, $tasks)) !== false) {
                    unset($tasks[$key]);
                }

                $projects[$projectCount]['tasks'][$taskCount]['project_group_id'] = $rawProject->project_group_id;
                $projects[$projectCount]['tasks'][$taskCount]['task_input_end_date'] = $rawProject->task_end_date;
                $projects[$projectCount]['tasks'][$taskCount]['taskable_id'] = $rawProject->taskable_id;
                $projects[$projectCount]['tasks'][$taskCount]['taskable_type'] = $rawProject->taskable_type;
                $projects[$projectCount]['tasks'][$taskCount]['task_completed'] = $rawProject->task_completed;
                $projects[$projectCount]['tasks'][$taskCount]['task_deadline_passed'] = $taskDeadlinePassed;
                $projects[$projectCount]['tasks'][$taskCount]['task_description'] = $rawProject->task_description;
                $projects[$projectCount]['tasks'][$taskCount]['task_id'] = $rawProject->task_id;
                $projects[$projectCount]['tasks'][$taskCount]['task_name'] = $rawProject->task_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_project_id'] = $rawProject->task_project_id;
                $projects[$projectCount]['tasks'][$taskCount]['team_id'] = $rawProject->team_id;
                $projects[$projectCount]['tasks'][$taskCount]['recipient_type'] = 'team';
                $projects[$projectCount]['tasks'][$taskCount]['assignee'] = $rawProject->team_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M y');
                $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M y');

                $taskDeadlinePassed
                ? $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = '-'.$taskDays
                : $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = $taskDays;
                
                // Compile team users
                if ($rawProject->member_type === 'App\\Models\\Team') {
                    $projects[$projectCount]['tasks'][$taskCount]['team_members'][$taskTeamMemberCount]['team_member_username'] = $rawProject->username;
                    // Profile pic?
                    ++$taskTeamMemberCount;
                }
            }

            // Task assigned to specific team member
            if ( ($rawProject->taskable_type === 'App\\Models\\Team') && 
                ($rawProject->task_user_id) && 
                ($rawProject->task_user_id === $rawProject->user_id) &&
                (in_array($rawProject->task_id, $tasks)) 
            ) {

                if (($key = array_search($rawProject->task_id, $tasks)) !== false) {
                    unset($tasks[$key]);
                }

                $projects[$projectCount]['tasks'][$taskCount]['project_group_id'] = $rawProject->project_group_id;
                $projects[$projectCount]['tasks'][$taskCount]['task_input_end_date'] = $rawProject->task_end_date;
                $projects[$projectCount]['tasks'][$taskCount]['taskable_id'] = $rawProject->taskable_id;
                $projects[$projectCount]['tasks'][$taskCount]['taskable_type'] = $rawProject->taskable_type;
                $projects[$projectCount]['tasks'][$taskCount]['task_completed'] = $rawProject->task_completed;
                $projects[$projectCount]['tasks'][$taskCount]['task_deadline_passed'] = $taskDeadlinePassed;
                $projects[$projectCount]['tasks'][$taskCount]['task_description'] = $rawProject->task_description;
                $projects[$projectCount]['tasks'][$taskCount]['task_id'] = $rawProject->task_id;
                $projects[$projectCount]['tasks'][$taskCount]['task_name'] = $rawProject->task_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_project_id'] = $rawProject->task_project_id;
                $projects[$projectCount]['tasks'][$taskCount]['team_id'] = $rawProject->team_id;
                $projects[$projectCount]['tasks'][$taskCount]['recipient_type'] = 'team member';
                $projects[$projectCount]['tasks'][$taskCount]['assignee'] = $rawProject->username;
                $projects[$projectCount]['tasks'][$taskCount]['task_user_id'] = $rawProject->task_user_id;
                $projects[$projectCount]['tasks'][$taskCount]['team_name'] = $rawProject->team_name;
                $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M y');
                $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M y');

                $taskDeadlinePassed
                ? $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = '-'.$taskDays
                : $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = $taskDays;
            }

            // Task has notes
            if ( ($rawProject->noteable_type === 'App\\Models\\Task') && 
                (in_array($rawProject->note_id, $notes))
            ) {

                if (($key = array_search($rawProject->note_id, $notes)) !== false) {
                    unset($notes[$key]);
                }

                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['note_id'] = $rawProject->note_id;
                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['note_body'] = $rawProject->note_body;
                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['note_created_at'] = Carbon::parse($rawProject->note_created_at)->format('d M y, H:i');
                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['note_author'] = $rawProject->note_user_id;

                ++$taskNoteCount;
            }


            if (!in_array($rawProject->task_id, $tasks)) {
                ++$taskCount;
            }

            $currentProjectId = $rawProject->project_id;
            ++$loop;
        }
        
        return $projects;
    }
}
