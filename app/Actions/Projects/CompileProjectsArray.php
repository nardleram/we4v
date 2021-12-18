<?php

namespace App\Actions\Projects;

use DateTime;
use Carbon\Carbon;

class CompileProjectsArray
{
    public function compileProjects($rawProjects) : array 
    {
        $projects = [];
        $teamMembers = [];
        $currentProjectId = 0;
        $currentTaskId = 0;
        $currentProjectNoteBody = '_EMPTY_9ugb1@/5%1_#\`"!§';
        $currentTaskNoteBody = '_EMPTY_9ugb1@/5%1_#\`"!§';
        $projectCount = 0;
        $taskCount = 0;
        $projectNoteCount = 0;
        $taskNoteCount = 0;
        $taskTeamMemberCount = 0;
        $loop = 0;
        $today = date("Y-m-d");
        $todayDate = new DateTime($today);
        $deadlinePassed = false;
        $taskDeadlinePassed = false;
        
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
            if ($rawProject->project_note_body !== $currentProjectNoteBody && $rawProject->project_note_body) {
                $projects[$projectCount]['notes'][$projectNoteCount]['note_body'] = $rawProject->project_note_body;
                $projects[$projectCount]['notes'][$projectNoteCount]['note_created_at'] = Carbon::parse($rawProject->project_note_created_at)->format('d M y, H:i');
                $projects[$projectCount]['notes'][$projectNoteCount]['note_author'] = $rawProject->note_author;

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
            if ($currentTaskId !== $rawProject->task_id) {

                if ($loop > 0) {
                    ++$taskCount;
                    $taskNoteCount = 0;
                    $teamMembers = [];
                    $taskTeamMemberCount = 0;
                }

                // Task assigned to group member
                if ( $rawProject->taskable_type === 'App\\Models\\Group' ) {

                    $projects[$projectCount]['tasks'][$taskCount]['project_group_id'] = $rawProject->project_group_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_input_end_date'] = $rawProject->task_end_date;
                    $projects[$projectCount]['tasks'][$taskCount]['taskable_id'] = $rawProject->taskable_id;
                    $projects[$projectCount]['tasks'][$taskCount]['taskable_type'] = $rawProject->taskable_type;
                    $projects[$projectCount]['tasks'][$taskCount]['group_name'] = $rawProject->project_task_group;
                    $projects[$projectCount]['tasks'][$taskCount]['recipient_type'] = 'group member';
                    $projects[$projectCount]['tasks'][$taskCount]['assignee'] = $rawProject->task_user_assignee;
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
                if ( $rawProject->taskable_type === 'App\\Models\\Team' && !$rawProject->task_user_assignee ){

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
                    $projects[$projectCount]['tasks'][$taskCount]['team_id'] = $rawProject->taskable_id;
                    $projects[$projectCount]['tasks'][$taskCount]['recipient_type'] = 'team';
                    $projects[$projectCount]['tasks'][$taskCount]['assignee'] = $rawProject->task_team;
                    $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M y');
                    $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M y');

                    $taskDeadlinePassed
                    ? $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = '-'.$taskDays
                    : $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = $taskDays;
                    
                }

                // Task assigned to specific team member
                if ( $rawProject->taskable_type === 'App\\Models\\Team' && $rawProject->task_user_assignee ) {
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
                    $projects[$projectCount]['tasks'][$taskCount]['team_id'] = $rawProject->task_team_id;
                    $projects[$projectCount]['tasks'][$taskCount]['recipient_type'] = 'team member';
                    $projects[$projectCount]['tasks'][$taskCount]['assignee'] = $rawProject->task_user_assignee;
                    $projects[$projectCount]['tasks'][$taskCount]['task_user_id'] = $rawProject->task_user_id;
                    $projects[$projectCount]['tasks'][$taskCount]['team_name'] = $rawProject->task_team;
                    $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M y');
                    $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M y');

                    $taskDeadlinePassed
                    ? $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = '-'.$taskDays
                    : $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = $taskDays;
                }
            }

            // Compile team users
            if ($rawProject->taskable_type === 'App\\Models\\Team' && !$rawProject->task_user_assignee) {

                if (!in_array($rawProject->team_member_username, $teamMembers, true)) {
                    array_push($teamMembers, $rawProject->team_member_username);
                    $projects[$projectCount]['tasks'][$taskCount]['team_members'][$taskTeamMemberCount]['team_member_username'] = $rawProject->team_member_username;
                    ++$taskTeamMemberCount;
                }

            }

            // Task has notes
            if ($rawProject->task_note_body !== $currentTaskNoteBody && $rawProject->task_note_body) {
                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['note_body'] = $rawProject->task_note_body;
                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['note_created_at'] = Carbon::parse($rawProject->task_note_created_at)->format('d M y, H:i');
                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['note_author'] = $rawProject->note_author;

                ++$taskNoteCount;
            }

            $currentProjectNoteBody = $rawProject->project_note_body;
            $currentTaskNoteBody = $rawProject->task_note_body;
            $currentProjectId = $rawProject->project_id;
            $currentTaskId = $rawProject->task_id;
            ++$loop;
        }
        
        return $projects;
    }
}