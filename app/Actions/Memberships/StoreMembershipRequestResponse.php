<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class StoreMembershipRequestResponse
{
    public function handle($request) : string
    {
        if ($request->confirmed) {
            Membership::where('membershipable_id', $request->membershipable_id)
                ->where('membershipable_type', $request->membershipable_type)
                ->where('user_id', auth()->id())
                ->update(array('confirmed' => $request->confirmed));

            $success = 'Invitation accepted';
        }

        if (!$request->confirmed) {
            Membership::where('membershipable_id', $request->membershipable_id)
                ->where('membershipable_type', $request->membershipable_type)
                ->where('user_id', auth()->id())
                ->delete();
            
            $success = 'Invitation declined';
            
            // Dispatch email to requester here
        }

        return $success;
    }
}