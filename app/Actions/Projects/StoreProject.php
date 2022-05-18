<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Http\Request;

class StoreProject
{
    public function handle(Request $request) : void
    {
        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner' => $request->owner,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'projectable_id' => $request->projectable_id,
            'projectable_type' => $request->projectable_type
        ]);
    }
}