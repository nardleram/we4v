<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Http\Request;

class UpdateProject
{
    public function handle(Request $request) : int
    {
        $project = Project::where('id', $request->id)->first();

        return $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'completed' => $request->completed,
            'end_date' => $request->end_date,
            'group_id' => $request->group_id ? $request->group_id : $project->group_id,
            'team_id' => $request->team_id ? $request->team_id : $project->team_id
        ]);
    }
}