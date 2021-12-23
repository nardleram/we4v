<?php

namespace App\Actions\Tasks;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetAdminTasks
{
    use SoftDeletes;

    private $compileProjectsArray;

    public function __construct(CompileTasksArray $compileTasksArray)
    {
        $this->compileTasksArray = $compileTasksArray;
    }
    
    public function handle($userId)
    {
        $rawIds = [];

        $ids = DB::select("SELECT 
            Te.id as team_id
        FROM memberships Me
        INNER JOIN teams Te
        ON Te.id = Me.membershipable_id
        WHERE Me.user_id = '$userId' AND Me.is_admin = true");

        foreach($ids as $id) {
            array_push($rawIds, $id->team_id);
        }
        
        if (count($rawIds) > 0) {
            $rawTasks = DB::select("SELECT
                Ta1.id as task_id,
                Ta1.name as task_name,
                Ta1.start_date as task_start_date,
                Ta1.end_date as task_end_date,
                Ta1.description as task_description,
                Ta1.completed as task_completed,
                Ta1.project_id as task_project_id,
                Ta1.owner as task_owner,
                Pr1.name as project_name,
                Pr1.description as project_description,
                Pr1.owner as project_owner,
                Pr1.start_date as project_start_date,
                Pr1.end_date as project_end_date,
                Pr1.completed as project_completed,
                Te1.name as team_name,
                Te1.id as team_id,
                No1.body as task_note_body,
                No1.id as task_note_id,
                No1.created_at as task_note_created_at,
                Us1.username as team_member_username,
                Us2.username as task_note_author,
                Us3.username as task_owner
            FROM tasks Ta1
            LEFT OUTER JOIN projects Pr1
            ON Ta1.project_id = Pr1.id
            LEFT OUTER JOIN notes No1
            ON No1.noteable_id = Ta1.id
            LEFT OUTER JOIN teams Te1
            ON Ta1.taskable_id = Te1.id
            LEFT OUTER JOIN memberships Me1
            ON Te1.id = Me1.membershipable_id
            LEFT OUTER JOIN users Us1
            ON Me1.user_id = Us1.id
            LEFT OUTER JOIN users Us2
            ON No1.user_id = Us2.id
            LEFT OUTER JOIN users Us3
            ON Ta1.owner = Us3.id
            WHERE Ta1.user_id IS NULL
            AND Ta1.taskable_id IN ('".implode("','",$rawIds)."')
            ORDER BY Ta1.end_date, No1.created_at asc");
            
            return $this->compileTasksArray->compileTasks($rawTasks);
        } else {
            return [];
        }
    }
}