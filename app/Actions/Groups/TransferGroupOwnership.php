<?php

namespace App\Actions\Groups;

use App\Models\Team;
use App\Models\Group;
use App\Models\Membership;
use App\Models\Project;
use App\Models\Task;
use App\Models\Vote;

class TransferGroupOwnership
{
    public function handle($request) : void
    {
        Group::where('id', $request->group_id)->update(['owner' => $request->user_id]);
        
        Team::where('group_id', $request->group_id)->update(['owner' => $request->user_id]);

        // Clean up memberships (owner should not also be member of Group or Team)
        Membership::where('membershipable_id', $request->group_id)
            ->where('user_id', $request->user_id)
            ->forceDelete();
        
        $teams = Team::where('group_id', $request->group_id)->get('id');

        foreach ($teams as $team) {
            Membership::where('membershipable_id', $team->id)
                ->where('user_id', $request->user_id)
                ->forceDelete();
        }

        Vote::where('group_id', $request->group_id)->update(['owner' => $request->user_id]);

        // Need project_ids to furnish relevant tasks with their new and sparkly owner_id
        $projects = Project::where('group_id', $request->group_id)
            ->where('owner', auth()->id())
            ->get(['id']);

        foreach ($projects as $project) {
            Task::where('project_id', $project->id)->update(['owner' => $request->user_id]);
        }

        Project::where('group_id', $request->group_id)
            ->where('owner', auth()->id())
            ->update(['owner' => $request->user_id]);
    }
}