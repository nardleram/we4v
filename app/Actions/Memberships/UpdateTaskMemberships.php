<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class UpdateTaskMemberships
{
    public function handle($request) : void
    {
        // Users may unclick (deselect) task assignees (thus effect a  
        // downstream delete action) and click (select) new ones.
        // Most efficient way to reflect their choices in DB is in two steps: 
        // 1. Delete all task assignees
        // 2. Simulate update. 
        // (The original created_at value is sent with the array of members
        // selected by the user.)

        Membership::where('membershipable_id', $request->membershipable_id)->forceDelete();

        foreach ($request->members as $member) {
            Membership::create([
                'membershipable_id' => $request->membershipable_id,
                'membershipable_type' => $request->membershipable_type,
                'user_id' => $member['user_id'],
                'created_at' => $member['created_at'] ? $member['created_at'] : now(),
                'role' => $member['role'],
                'updated_at' => now(),
                'updated_by' => auth()->id(),
                'deleted_at' => null,
                'is_admin' => false,
                'confirmed' => false
            ]);
        }
    }
}