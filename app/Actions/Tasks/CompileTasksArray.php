<?php

namespace App\Actions\Tasks;

use DateTime;
use Carbon\Carbon;

class CompileTasksArray
{
    public function compileTasks($rawTasks) : array
    {
        $tasks = [];
        $teamMembers = [];
        $taskCount = 0;
        $noteCount = 0;
        $teamMemberCount = 0;
        $loop = 0;
        $currentTaskId = 0;
        $currentNoteId = 0;
        $today = date("Y-m-d");
        $todayDate = new DateTime($today);
        $taskDeadlinePassed = false;

        forEach($rawTasks as $rawTask) {
            if ($currentTaskId !== $rawTask->task_id) {
                if ($loop > 0) {
                    ++$taskCount;
                    $teamMemberCount = 0;
                    $noteCount = 0;
                    $teamMembers = [];
                }

                $taskDeadlinePassed = false;
                $taskToDate = new DateTime($rawTask->task_end_date);
                $taskInterval = $todayDate->diff($taskToDate);
                $taskDays = $taskInterval->format('%a');
                if ($todayDate > $taskToDate) {
                    $taskDeadlinePassed = true;
                }

                $tasks[$taskCount]['task_name'] = $rawTask->task_name;
                $tasks[$taskCount]['task_completed'] = $rawTask->task_completed;
                $tasks[$taskCount]['task_description'] = $rawTask->task_description;
                $tasks[$taskCount]['task_id'] = $rawTask->task_id;
                $tasks[$taskCount]['task_project_id'] = $rawTask->task_project_id;
                $tasks[$taskCount]['task_start_date'] = Carbon::parse($rawTask->task_start_date)->format('d M Y');
                $tasks[$taskCount]['task_end_date'] = Carbon::parse($rawTask->task_end_date)->format('d M Y');
                $tasks[$taskCount]['task_input_end_date'] = $rawTask->task_end_date;
                $tasks[$taskCount]['task_owner'] = $rawTask->task_owner;
                $tasks[$taskCount]['task_deadline_passed'] = $taskDeadlinePassed;
                $tasks[$taskCount]['project_start_date'] = $rawTask->project_start_date;
                $tasks[$taskCount]['project_end_date'] = $rawTask->project_end_date;
                $tasks[$taskCount]['project_name'] = $rawTask->project_name;
                $tasks[$taskCount]['team_id'] = $rawTask->team_id;
                $tasks[$taskCount]['team_name'] = $rawTask->team_name;

                $taskDeadlinePassed
                ? $tasks[$taskCount]['task_days_remaining'] ='-'.$taskDays
                : $tasks[$taskCount]['task_days_remaining'] = $taskDays;
            }

            // Team members
            if (!in_array($rawTask->team_member_username, $teamMembers)) {
                array_push($teamMembers, $rawTask->team_member_username);

                $tasks[$taskCount]['team_members'][$teamMemberCount]['username'] = $rawTask->team_member_username;

                ++$teamMemberCount;
            }

            // Task notes
            if ($currentNoteId !== $rawTask->task_note_id) {
                $tasks[$taskCount]['task_notes'][$noteCount]['note_body'] = $rawTask->task_note_body;
                $tasks[$taskCount]['task_notes'][$noteCount]['note_author'] = $rawTask->task_note_author;
                $tasks[$taskCount]['task_notes'][$noteCount]['note_created_at'] = Carbon::parse($rawTask->task_note_created_at)->format('d M Y H:i');

                ++$noteCount;
            }

            ++$loop;
            $currentTaskId = $rawTask->task_id;
            $currentNoteId = $rawTask->task_note_id;
        }
        
        return $tasks;
    }
}