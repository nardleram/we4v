<?php

namespace App\Actions\Projects;

use App\Models\Project;
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
        $rawProjects = Project::where('projects.owner', $userId)
        ->leftJoin('tasks AS Ta1', function ($join) {
            $join->on('Ta1.project_id', '=', 'projects.id');
        })
        ->leftJoin('memberships AS Me1', function ($join) {
            $join->on('Me1.membershipable_id', '=', 'Ta1.id');
        })
        ->leftJoin('groups AS Gr1', function ($join) {
            $join->on('Gr1.id', '=', 'projects.group_id');
        })
        ->leftJoin('groups AS Gr2', function ($join) {
            $join->on('Gr2.id', '=', 'Ta1.taskable_id');
        })
        ->leftJoin('teams AS Te1', function ($join) {
            $join->on('Te1.id', '=', 'projects.team_id');
        })
        ->leftJoin('teams AS Te2', function ($join) {
            $join->on('Te2.id', '=', 'Ta1.taskable_id');
        })
        ->leftJoin('notes AS No1', function ($join) {
            $join->on('No1.noteable_id', '=', 'projects.id');
        })
        ->leftJoin('notes AS No2', function ($join) {
            $join->on('No2.noteable_id', '=', 'Ta1.id');
        })
        ->leftJoin('users AS Us1', function ($join) {
            $join->on('No1.user_id', '=', 'Us1.id');
        })
        ->leftJoin('users AS Us2', function ($join) {
            $join->on('No2.user_id', '=', 'Us2.id');
        })
        ->leftJoin('users AS Us3', function ($join) {
            $join->on('Us3.id', '=', 'Me1.user_id');
        })
        ->select([
            'projects.id AS project_id',
            'projects.name AS project_name',
            'projects.description AS project_description',
            'projects.start_date AS project_start_date',
            'projects.end_date AS project_end_date',
            'projects.completed AS project_completed',
            'projects.group_id AS project_group_id',
            'projects.team_id AS project_team_id',
            'projects.updated_at AS project_updated_at',
            'Ta1.id AS task_id',
            'Ta1.name AS task_name',
            'Ta1.description AS task_description',
            'Ta1.project_id AS task_project_id',
            'Ta1.start_date AS task_start_date',
            'Ta1.end_date AS task_end_date',
            'Ta1.completed AS task_completed',
            'Ta1.taskable_id AS taskable_id',
            'Ta1.taskable_type AS taskable_type',
            'Ta1.updated_at AS task_updated_at',
            'Gr1.name AS project_group_name',
            'Gr2.name AS task_group_name',
            'Te1.name AS project_team_name',
            'Te2.name AS task_team_name',
            'Te2.id AS task_team_id',
            'No1.id AS project_note_id',
            'No1.body AS project_note_body',
            'No1.created_at AS project_note_created_at',
            'Us1.username AS project_note_author',
            'No2.id AS task_note_id',
            'No2.body AS task_note_body',
            'No2.created_at AS task_note_created_at',
            'Us2.username AS task_note_author',
            'Us3.username AS task_member_username',
            'Us3.id AS task_member_user_id',
            'Me1.created_at AS task_member_created_at'
        ])
        ->groupBy([
            'projects.id',
            'Ta1.id',
            'Gr1.name',
            'Gr2.name',
            'Te1.name',
            'Te2.name',
            'Te2.id',
            'No1.id',
            'No1.body',
            'No1.created_at',
            'No2.id',
            'No2.body',
            'Us1.username',
            'Us1.id',
            'Us2.username',
            'Us2.id',
            'Us3.username',
            'Us3.id',
            'Me1.created_at'
        ])
        ->orderBy('projects.end_date')
        ->orderBy('Ta1.end_date')
        ->orderBy('No1.created_at', 'ASC')
        ->orderBy('No2.created_at', 'ASC')
        ->get();

        return $this->compileProjectsArray->compileProjects($rawProjects);
    }
}