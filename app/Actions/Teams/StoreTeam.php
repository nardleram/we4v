<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\Membership;
use Illuminate\Http\Request;

class StoreTeam
{
    public function storeTeam(Request $request) : object
    {
        return Team::create([
            'name' => $request->name,
            'function' => $request->function,
            'owner' => $request->owner,
            'group_id' => $request->group_id
        ]);
    }

    public function storeMembers(Request $request, $teamId) : void
    {
        foreach ($request->assocs as $assoc) {
            Membership::create([
                'membershipable_id' => $teamId,
                'membershipable_type' => $request->membership_type,
                'user_id' => $assoc,
                'role' => 'boss'
            ]);
        }
    }
}