<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\Group;
use App\Models\Membership;

class DestroyTeamCascade
{
    public function handle($id) : void
    {
        $team = Team::findOrFail($id);

        $team->delete();

        Membership::where('membershipable_id', $id)
            ->where('membershipable_type', 'App\Models\Team')
            ->delete();

        $groupIds = Group::where('team_id', $id)->get(['id']);

        $groupIds->count() > 0 
            ? Membership::destroy($groupIds)
            : null;
        
        Group::where('team_id', $id)->delete();
    }
}