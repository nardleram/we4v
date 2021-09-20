<?php

namespace App\Actions\Memberships;

use App\Models\User;
use App\Models\Membership;

class StoreMembershipRequestResponses
{
    public function handle($request)
    {
        if ($request->confirmed) {
            Membership::where('membershipable_id', $request->membershipable_id)
                ->where('membershipable_type', $request->membershipable_type)
                ->where('user_id', auth()->id())
                ->update(array('confirmed' => $request->confirmed));
        }

        if (!$request->confirmed) {
            Membership::where('membershipable_id', $request->membershipable_id)
                ->where('membershipable_type', $request->membershipable_type)
                ->where('user_id', auth()->id())
                ->delete();
        }

        $user = User::findOrFail($request->requester)->only('username');

        return $user;
    }
}