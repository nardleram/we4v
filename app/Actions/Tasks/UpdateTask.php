<?php

namespace App\Actions\Tasks;

use App\Models\Task;
use Illuminate\Http\Request;

class UpdateTask
{
    public function handle(Request $request) : int
    {
        return Task::where('id', $request->id)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'end_date' => $request->end_date,
                'end_date' => $request->end_date,
                'taskable_type' => $request->taskable_type,
                'completed' => $request->completed,
            ]);
    }
}