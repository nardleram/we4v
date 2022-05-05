<?php

namespace App\Actions\Memberships;

use App\Jobs\ThrottleMail;
use App\Models\Membership;
use App\Mail\MembershipDeleted;
use App\Mail\MembershipUpdated;
use App\Mail\TeamMembershipRequested;
use App\Mail\GroupMembershipRequested;
use Illuminate\Support\Facades\Mail;

class UpdateMemberships
{
    public function handle($request) : void
    {
        // Force-delete de-checked members
        $db_ids = [];
        $memb_ids = [];
        $delete_these_members = [];
        $roleChanged = false;
        $adminChanged = false;

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
                $membership = Membership::where('user_id', $delete_this_member)
                    ->where('membershipable_id', $request->membershipable_id)
                    ->first();

                // Dispatch notification email immediately, before membership deletion
                // ThrottleMail::dispatch(new MembershipDeleted($membership), $membership->user);
                Mail::to($membership->user)->send(
                    new MembershipDeleted($membership
                ));

                Membership::where('membershipable_id', $request->membershipable_id)
                    ->where('user_id', $delete_this_member)
                    ->forceDelete();

            }
        }

        // Update/create existing/new members
        foreach ($request->members as $member) {
            if ($member['invited']) { // Existing member: update if changes to record
                $currentMember = Membership::where('membershipable_id', $request->membershipable_id)
                    ->where('user_id', $member['user_id'])
                    ->first();

                if ($currentMember 
                    && ($currentMember->role !== $member['role'] || $currentMember->is_admin !== $member['is_admin'])
                ) {
                    Membership::where('membershipable_id', $request->membershipable_id)
                        ->where('user_id', $member['user_id'])
                        ->update([
                            'role' => $member['role'],
                            'is_admin' => $member['is_admin'],
                            'confirmed' => false,
                            'updated_by' => auth()->id()
                        ]);
                    
                    // Dispatch notification email
                    if ($currentMember->role !== $member['role']) {
                        $roleChanged = true;
                    }
                    if ($currentMember->is_admin !== $member['is_admin']) {
                        $adminChanged = true;
                    }

                    ThrottleMail::dispatch(new MembershipUpdated($currentMember, $roleChanged, $adminChanged), $currentMember->user);
                }
            }
            
            if (!$member['invited']) { // New member
                $membership = Membership::create([
                    'membershipable_id' => $request->membershipable_id,
                    'membershipable_type' => $request->membershipable_type,
                    'user_id' => $member['user_id'],
                    'updated_by' => null,
                    'role' => $member['role'],
                    'is_admin' => $member['is_admin'],
                    'confirmed' => false
                ]);

                if ($request->membershipable_type === 'App\\Models\\Group') {
                    ThrottleMail::dispatch(new GroupMembershipRequested($membership), $membership->user);
                }
    
                if ($request->membershipable_type === 'App\\Models\\Team') {
                    ThrottleMail::dispatch(new TeamMembershipRequested($membership), $membership->user);
                }
            }
        }
    }
}