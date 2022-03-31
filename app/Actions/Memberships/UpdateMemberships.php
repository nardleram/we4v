<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class UpdateMemberships
{
    public function handle($request) : void
    {
        // Force-delete de-checked members
        $db_ids = [];
        $memb_ids = [];
        $delete_these_members = [];

        $members = Membership::where('membershipable_id', $request->membershipable_id)->withTrashed()
            ->get(['user_id']);
        
        foreach ($members as $member) {
            array_push($db_ids, $member->user_id);
        }

        foreach ($request->members as $member) {
            array_push($memb_ids, $member['user_id']);
        }
        
        foreach($db_ids as $id) {
            if (!in_array($id, $memb_ids)) {
                array_push($delete_these_members, $id);
            }
        }

        if (count($delete_these_members) > 0) {
            foreach($delete_these_members as $delete_this_member) {
                Membership::where('membershipable_id', $request->membershipable_id)
                    ->where('user_id', $delete_this_member)
                    ->forceDelete();
            }
        }

        // Update or create existing or new members
        foreach ($request->members as $member) {
            if ($member['invited']) { // Existing member: update if changes to record
                $currentMember = Membership::where('membershipable_id', $request->membershipable_id)
                    ->where('user_id', $member['user_id'])
                    ->first(['role', 'is_admin']);

                
                if ($currentMember && ($currentMember->role !== $member['role'] || $currentMember->is_admin !== $member['is_admin'])) {
                    Membership::where('membershipable_id', $request->membershipable_id)
                        ->where('user_id', $member['user_id'])
                        ->update([
                            'role' => $member['role'],
                            'is_admin' => $member['is_admin'],
                            'confirmed' => false, // Issue new invitation
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
                    'is_admin' => $member['is_admin'],
                    'confirmed' => false
                ]);
            }
        }
    }
}