<?php

namespace App\Actions\Memberships;

use App\Models\User;
use App\Jobs\ThrottleMail;
use App\Models\Membership;
use App\Mail\TeamMembershipRequested;
use App\Mail\GroupMembershipRequested;

class StoreMemberships
{
    public function handle($request, $parentId)
    {
        foreach ($request->members as $member) {
            $membership = Membership::create([
                'membershipable_id' => $parentId,
                'membershipable_type' => $request->membershipable_type,
                'user_id' => $member['user_id'],
                'role' => $member['role'],
                'is_admin' => $member['is_admin'],
                'updated_by' => auth()->id()
            ]);

            $user = User::where('id', auth()->id())->first();
        
            if ($request->membershipable_type === 'App\\Models\\Group') {
                ThrottleMail::dispatch(new GroupMembershipRequested($membership, $user), $membership->user);
            }

            if ($request->membershipable_type === 'App\\Models\\Team') {
                ThrottleMail::dispatch(new TeamMembershipRequested($membership, $user), $membership->user);
            }
        }
    }
}