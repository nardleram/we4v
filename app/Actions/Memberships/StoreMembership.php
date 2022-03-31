<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class StoreMembership
{
    public function handle($request) : void
    {
        Membership::create([
            'membershipable_id' => $request->membershipable_id,
            'membershipable_type' => $request->membershipable_type,
            'user_id' => $request->user_id,
            'group_id' => $request->group_id,
            'role' => $request->role,
            'is_admin' => $request->is_admin,
            'updated_by' => auth()->id()
        ]);
    }
}