<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Http\Request;

class UpdateProject
{
    public function handle(Request $request) : int
    {
        return Project::where('id', $request->id)
            ->update([
                'end_date' => $request->end_date,
                'group_id' => $request->group_id,
                'team_id' => $request->team_id,
            ]);
    }
}