<?php

namespace App\Actions\Groups;

use App\Models\Team;
use App\Models\Group;
use App\Models\Membership;

class DestroyGroupCascade
{
    public function handle($id)
    {
        try {
            $group = group::findOrFail($id);
        } catch (\Exception $exception) {
            return view('groups.notFound');
        }

        $group->delete();

        Membership::where('membershipable_id', $id)
            ->where('membershipable_type', 'App\Models\Group')
            ->delete();

        $teamIds = Team::where('group_id', $id)->get(['id']);

        $teamIds->count() > 0 
            ? Membership::destroy($teamIds)
            : null;
        
        Team::where('group_id', $id)->delete();
    }
}