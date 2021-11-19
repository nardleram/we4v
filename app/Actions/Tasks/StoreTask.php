<?php

namespace App\Actions\Tasks;

use App\Models\Task;
use Illuminate\Http\Request;

class StoreTask
{
    public function handle(Request $request) : void
    {
        Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner' => $request->owner,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'project_id' => $request->project_id,
            'user_id' => $request->user_id,
            'taskable_id' => $request->taskable_id,
            'taskable_type' => $request->taskable_type
        ]);
    }
}