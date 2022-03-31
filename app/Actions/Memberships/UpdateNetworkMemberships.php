<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class UpdateNetworkMemberships
{
    public function handle($request) : void
    {
        // Users may unclick (ans thus effect a 'delete' of) network groups
        // and click (select) new ones.
        // Most resource-efficient way to reflect their choices in DB is in two steps: 
        // 1. Delete all network groups
        // 2. Simulate update via create where needed. 
        // (The original created_at value is sent with the array of groups
        // selected by the user.)

        Membership::where('membershipable_id', $request->id)->forceDelete();

        foreach ($request->groups as $group) {
            Membership::create([
                'membershipable_id' => $request->id,
                'membershipable_type' => $request->membershipable_type,
                'group_id' => $group['group_id'],
                'created_at' => $group['membership_created_at'] ? $group['membership_created_at'] : now(),
                'role' => null,
                'updated_by' => auth()->id(),
                'deleted_at' => null,
                'is_admin' => false,
                'confirmed' => $group['membership_confirmed'],
            ]);
        }
    }
}