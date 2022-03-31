<?php

namespace App\Actions\Groups;

use App\Models\Team;
use App\Models\Group;
use App\Models\Project;
use App\Models\Task;
use App\Models\Vote;

class TransferGroupOwnership
{
    public function handle($request) : void
    {
        Group::where('id', $request->group_id)->update(['owner' => $request->user_id]);
        
        Team::where('group_id', $request->group_id)->update(['owner' => $request->user_id]);

        Vote::where('group_id', $request->group_id)->update(['owner' => $request->user_id]);
        
        Project::where('group_id', $request->group_id)
            ->where('owner', auth()->id())
            ->update(['owner' => $request->user_id]);

        // Need project_ids to furnish relevant tasks with their new and sparkly owner_id
        $project_ids = Project::where('group_id', $request->group_id)
            ->where('owner', auth()->id())
            ->get(['id']);

        foreach ($project_ids as $project_id) {
            Task::where('project_id', $project_id)->update(['owner' => $request->user_id]);
        }
    }
}