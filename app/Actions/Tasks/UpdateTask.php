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
                'end_date' => $request->end_date,
                'user_id' => $request->user_id,
                'taskable_type' => $request->taskable_type
            ]);
    }
}