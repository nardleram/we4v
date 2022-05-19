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
        $selectedMembers = [];
        $projectNotes = [];
        $taskNotes = [];
        $currentProjectId = 0;
        $currentTaskId = 0;
        $currentProjectNoteId = 0;
        $currentTaskNoteId = 0;
        $projectCount = 0;
        $taskCount = 0;
        $projectNoteCount = 0;
        $taskNoteCount = 0;
        $selectedMemberCount = 0;
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
                    $selectedMemberCount = 0;
                    $projectNotes = [];
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
                $projects[$projectCount]['projectable_id'] = $rawProject->projectable_id;
                $projects[$projectCount]['projectable_type'] = $rawProject->projectable_type;
                $projects[$projectCount]['project_updated_at'] = Carbon::parse($rawProject->project_updated_at)->format('d M Y');
                $projects[$projectCount]['project_start_date'] = Carbon::parse($rawProject->project_start_date)->format('d M Y');
                $projects[$projectCount]['project_end_date'] = Carbon::parse($rawProject->project_end_date)->format('d M Y');
                $projects[$projectCount]['project_input_end_date'] = $rawProject->project_end_date;
                $projects[$projectCount]['project_group_name'] = $rawProject->project_group_name;
                $projects[$projectCount]['project_team_name'] = $rawProject->project_team_name;
                $projects[$projectCount]['project_deadline_passed'] = $deadlinePassed;
                $deadlinePassed 
                ? $projects[$projectCount]['project_days_remaining'] = '-'.$projectDays
                : $projects[$projectCount]['project_days_remaining'] = $projectDays;
            }

            // Project has notes
            if (( $rawProject->project_note_id !== $currentProjectNoteId ) 
                && $rawProject->project_note_body 
                && !in_array($rawProject->project_note_id, $projectNotes)) {
                array_push($projectNotes, $rawProject->project_note_id);

                $projects[$projectCount]['notes'][$projectNoteCount]['project_note_body'] = $rawProject->project_note_body;
                $projects[$projectCount]['notes'][$projectNoteCount]['project_note_created_at'] = Carbon::parse($rawProject->project_note_created_at)->format('d M y, H:i');
                $projects[$projectCount]['notes'][$projectNoteCount]['project_note_author'] = $rawProject->project_note_author;

                ++$projectNoteCount;
            }

            // TASKS
            $taskDeadlinePassed = false;
            $taskTeamMemberCount = 0;
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
                    $taskTeamMemberCount = 0;
                    $selectedMemberCount = 0;
                    $teamMembers = [];
                    $taskNotes = [];
                    $selectedMembers = [];
                }

                // Task assigned to group member(s)
                if ( $rawProject->taskable_type === 'App\\Models\\Group' ) {
                    $projects[$projectCount]['tasks'][$taskCount]['task_id'] = $rawProject->task_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_name'] = $rawProject->task_name;
                    $projects[$projectCount]['tasks'][$taskCount]['task_description'] = $rawProject->task_description;
                    $projects[$projectCount]['tasks'][$taskCount]['projectable_id'] = $rawProject->projectable_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_input_end_date'] = $rawProject->task_end_date;
                    $projects[$projectCount]['tasks'][$taskCount]['taskable_id'] = $rawProject->taskable_id;
                    $projects[$projectCount]['tasks'][$taskCount]['taskable_type'] = $rawProject->taskable_type;
                    $projects[$projectCount]['tasks'][$taskCount]['group_name'] = $rawProject->project_task_group;
                    $projects[$projectCount]['tasks'][$taskCount]['recipient_type'] = 'group member';
                    $projects[$projectCount]['tasks'][$taskCount]['task_completed'] = $rawProject->task_completed;
                    $projects[$projectCount]['tasks'][$taskCount]['task_deadline_passed'] = $taskDeadlinePassed;
                    $projects[$projectCount]['tasks'][$taskCount]['task_team_id'] = $rawProject->task_team_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_team_name'] = $rawProject->task_team_name;
                    $projects[$projectCount]['tasks'][$taskCount]['task_project_id'] = $rawProject->task_project_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_updated_at'] = Carbon::parse($rawProject->task_updated_at)->format('d M y');
                    $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M y');
                    $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M y');

                    $projects[$projectCount]['tasks'][$taskCount]['selected_group_members'][$selectedMemberCount]['task_member_username'] = $rawProject->task_member_username;
                    $projects[$projectCount]['tasks'][$taskCount]['selected_group_members'][$selectedMemberCount]['task_member_user_id'] = $rawProject->task_member_user_id;

                    $taskDeadlinePassed
                    ? $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = '-'.$taskDays
                    : $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = $taskDays;
                }

                // Task assigned to whole team
                if ( $rawProject->taskable_type === 'App\\Models\\Team' && !$rawProject->task_member_user_id ){

                    $projects[$projectCount]['tasks'][$taskCount]['projectable_id'] = $rawProject->projectable_id;
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
                    $projects[$projectCount]['tasks'][$taskCount]['assignee'] = $rawProject->task_team_name;
                    $projects[$projectCount]['tasks'][$taskCount]['task_updated_at'] = Carbon::parse($rawProject->task_updated_at)->format('d M y');
                    $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M y');
                    $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M y');

                    $taskDeadlinePassed
                    ? $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = '-'.$taskDays
                    : $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = $taskDays;
                    
                }

                // Task assigned to selected team member(s)
                if ( $rawProject->taskable_type === 'App\\Models\\Team' && $rawProject->task_member_username ) {
                    $projects[$projectCount]['tasks'][$taskCount]['projectable_id'] = $rawProject->projectable_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_input_end_date'] = $rawProject->task_end_date;
                    $projects[$projectCount]['tasks'][$taskCount]['taskable_id'] = $rawProject->taskable_id;
                    $projects[$projectCount]['tasks'][$taskCount]['taskable_type'] = $rawProject->taskable_type;
                    $projects[$projectCount]['tasks'][$taskCount]['task_completed'] = $rawProject->task_completed;
                    $projects[$projectCount]['tasks'][$taskCount]['task_deadline_passed'] = $taskDeadlinePassed;
                    $projects[$projectCount]['tasks'][$taskCount]['task_description'] = $rawProject->task_description;
                    $projects[$projectCount]['tasks'][$taskCount]['task_id'] = $rawProject->task_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_name'] = $rawProject->task_name;
                    $projects[$projectCount]['tasks'][$taskCount]['task_project_id'] = $rawProject->task_project_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_team_id'] = $rawProject->task_team_id;
                    $projects[$projectCount]['tasks'][$taskCount]['task_team_name'] = $rawProject->task_team_name;
                    $projects[$projectCount]['tasks'][$taskCount]['task_start_date'] = Carbon::parse($rawProject->task_start_date)->format('d M y');
                    $projects[$projectCount]['tasks'][$taskCount]['task_updated_at'] = Carbon::parse($rawProject->task_updated_at)->format('d M y');
                    $projects[$projectCount]['tasks'][$taskCount]['task_end_date'] = Carbon::parse($rawProject->task_end_date)->format('d M y');

                    $taskDeadlinePassed
                    ? $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = '-'.$taskDays
                    : $projects[$projectCount]['tasks'][$taskCount]['task_days_remaining'] = $taskDays;
                }
            }

            // Compile team members (whole team as assignee) // I think I don't need this block
            if ($rawProject->taskable_type === 'App\\Models\\Team' && !$rawProject->task_member_user_id) {
                if (!in_array($rawProject->team_member_username, $teamMembers, true)) {
                    array_push($teamMembers, $rawProject->team_member_username);
                    $projects[$projectCount]['tasks'][$taskCount]['team_members'][$taskTeamMemberCount]['team_member_username'] = $rawProject->team_member_username;
                    ++$taskTeamMemberCount;
                }
            }

            // Compile selected users (= assignee(s))
            if ($rawProject->task_member_user_id) {
                if (!in_array($rawProject->task_member_user_id, $selectedMembers, true)) {
                    array_push($selectedMembers, $rawProject->task_member_user_id);
                    $projects[$projectCount]['tasks'][$taskCount]['selected_task_members'][$selectedMemberCount]['task_member_username'] = $rawProject->task_member_username;
                    $projects[$projectCount]['tasks'][$taskCount]['selected_task_members'][$selectedMemberCount]['task_member_created_at'] = $rawProject->task_member_created_at;
                    $projects[$projectCount]['tasks'][$taskCount]['selected_task_members'][$selectedMemberCount]['task_member_user_id'] = $rawProject->task_member_user_id;
                    $projects[$projectCount]['tasks'][$taskCount]['selected_task_members'][$selectedMemberCount]['task_team_name'] = $rawProject->task_team_name;
                    ++$selectedMemberCount;
                }
            }

            // Task has notes
            if ($rawProject->task_note_id !== $currentTaskNoteId && $rawProject->task_note_body && !in_array($rawProject->task_note_id, $taskNotes)) {
                array_push($taskNotes, $rawProject->task_note_id);

                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['task_note_body'] = $rawProject->task_note_body;
                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['task_note_created_at'] = Carbon::parse($rawProject->task_note_created_at)->format('d M y, H:i');
                $projects[$projectCount]['tasks'][$taskCount]['notes'][$taskNoteCount]['task_note_author'] = $rawProject->task_note_author;

                ++$taskNoteCount;
            }

            $currentProjectNoteId = $rawProject->project_note_id;
            $currentTaskNoteId = $rawProject->task_note_id;
            $currentProjectId = $rawProject->project_id;
            $currentTaskId = $rawProject->task_id;
            ++$loop;
        }
        dd($projects);
        return $projects;
    }
}