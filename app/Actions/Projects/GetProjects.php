<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetProjects
{
    use SoftDeletes;
    private $compileProjectsArray;

    public function __construct(CompileProjectsArray $compileProjectsArray)
    {
        $this->compileProjectsArray = $compileProjectsArray;
    }

    public function handle($userId)
    {
        $rawProjects = DB::select("SELECT Pr1.name as project_name,
            Pr1.id as project_id,
            Pr1.completed as project_completed,
            Pr1.description as project_description,
            Pr1.group_id as project_group_id,
            Pr1.start_date as project_start_date,
            Pr1.end_date as project_end_date,
            Ta1.id as task_id,
            Ta1.name as task_name,
            Ta1.description as task_description,
            Ta1.start_date as task_start_date,
            Ta1.end_date as task_end_date,
            Ta1.taskable_id as taskable_id,
            Ta1.taskable_type as taskable_type,
            Ta1.completed as task_completed,
            Ta1.user_id as task_user_id,
            Ta1.project_id as task_project_id,
            Te1.name as task_team,
            Te1.id as task_team_id,
            Gr1.name as project_task_group,
            Gr1.id as project_task_group_id,
            Us1.username as task_user_assignee,
            No1.body as task_note_body,
            No1.id as task_note_id,
            No1.created_at as task_note_created_at,
            No2.body as project_note_body,
            No2.id as project_note_id,
            No2.created_at as project_note_created_at,
            Us2.username as task_note_author,
            Us3.username as project_note_author,
            Us4.username as group_member_username,
            Us5.username as team_member_username,
            Gr2.name as project_group_name
        FROM projects Pr1
        LEFT OUTER JOIN tasks Ta1
        ON Ta1.project_id = Pr1.id
        LEFT OUTER JOIN teams Te1
        ON Te1.id = Ta1.taskable_id
        LEFT OUTER JOIN groups Gr1
        ON Gr1.id = Ta1.taskable_id
        LEFT OUTER JOIN users Us1
        ON Us1.id = Ta1.user_id
        LEFT OUTER JOIN notes No1
        ON No1.noteable_id = Ta1.id
        LEFT OUTER JOIN notes No2
        ON No2.noteable_id = Pr1.id
        LEFT OUTER JOIN users Us2
        ON Us2.id = No1.user_id
        LEFT OUTER JOIN users Us3
        ON Us3.id = No2.user_id
        LEFT OUTER JOIN memberships Me1
        ON Me1.membershipable_id = Gr1.id
        LEFT OUTER JOIN memberships Me2
        ON Me2.membershipable_id = Te1.id
        LEFT OUTER JOIN users Us4
        ON Us3.id = Me1.user_id
        LEFT OUTER JOIN users Us5
        ON Us4.id = Me2.user_id
        INNER JOIN groups Gr2
        ON Gr2.id = Pr1.group_id
        WHERE Pr1.owner = '$userId'
        ORDER BY Pr1.end_date, Ta1.end_date, No1.created_at asc, No2.created_at asc");
        
        return $this->compileProjectsArray->compileProjects($rawProjects);
    }
}