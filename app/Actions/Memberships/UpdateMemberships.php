<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class UpdateMemberships
{
    public function handle($request)
    {
        foreach ($request->members as $member) {
            if ($member['invited']) {
                Membership::where('membershipable_id', $request->membershipable_id)
                ->where('membershipable_type', $request->membershipable_type)
                ->where('user_id', $member['user_id'])
                ->update([
                    'role' => $member['role'],
                    'is_admin' => $member['admin'],
                    'confirmed' => false
                ]);
            }
            
            if (!$member['invited']) {
                Membership::create([
                    'membershipable_id' => $request->membershipable_id,
                    'membershipable_type' => $request->membershipable_type,
                    'user_id' => $member['user_id'],
                    'role' => $member['role'],
                    'is_admin' => $member['admin'],
                    'confirmed' => false
                ]);
            }
        }
    }
}