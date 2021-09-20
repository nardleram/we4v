<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class StoreMemberships
{
    public function handle($request)
    {
        foreach ($request->assocs as $assoc) {
            Membership::create([
                'membershipable_id' => $request->team_id,
                'membershipable_type' => $request->type,
                'user_id' => $assoc->user_id,
                'role' => 'boss'
            ]);
        }
    }
}