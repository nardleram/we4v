<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class UpdateMembershipRequestResponse
{
    public function handle($request) : string
    {
        if ($request->confirmed) {
            Membership::where('membershipable_id', $request->membershipable_id)
                ->where('user_id', auth()->id())
                ->update(array('confirmed' => $request->confirmed));

            $success = 'Invitation accepted';
        }

        if (!$request->confirmed) {
            Membership::where('membershipable_id', $request->membershipable_id)
                ->where('user_id', auth()->id())
                ->delete();
            
            $success = 'Invitation declined';
            
            // Dispatch email to requester here
        }

        return $success;
    }
}