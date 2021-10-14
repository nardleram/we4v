<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class StoreMemberships
{
    public function storeMembers($request, $parentId)
    {
        foreach ($request->members as $member) {
            Membership::create([
                'membershipable_id' => $parentId,
                'membershipable_type' => $request->membershipable_type,
                'user_id' => $member['user_id'],
                'role' => $member['role'],
                'is_admin' => $member['admin']
            ]);
        }
    }
}