<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class UpdateMemberships
{
    public function handle($request) : void
    {
        foreach ($request->members as $member) {
            if ($member['invited']) { // Existing member
                $currentMember = Membership::where('membershipable_id', $request->membershipable_id)
                    ->where('user_id', $member['user_id'])
                    ->get(['role', 'is_admin']);

                
                if ($currentMember[0]->role !== $member['role'] || $currentMember[0]->is_admin !== $member['is_admin']) {
                    Membership::where('membershipable_id', $request->membershipable_id)
                        ->where('user_id', $member['user_id'])
                        ->update([
                            'role' => $member['role'],
                            'is_admin' => $member['admin'],
                            'confirmed' => false,
                            'updated_by' => auth()->id()
                        ]);
                }
            }
            
            if (!$member['invited']) { // New member
                Membership::create([
                    'membershipable_id' => $request->membershipable_id,
                    'membershipable_type' => $request->membershipable_type,
                    'user_id' => $member['user_id'],
                    'updated_by' => null,
                    'role' => $member['role'],
                    'is_admin' => $member['admin'],
                    'confirmed' => false
                ]);
            }
        }
    }
}