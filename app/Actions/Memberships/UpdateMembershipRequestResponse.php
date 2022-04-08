<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class UpdateMembershipRequestResponse
{
    public function handle($request) : string
    {
        $success = '';

        if ($request->confirmed && $request->user_id) {
            Membership::where('id', $request->id)
                ->where('user_id', $request->user_id)
                ->update(array('confirmed' => $request->confirmed));

            $success = 'Invitation accepted';
        }

        if ($request->confirmed && $request->group_id) {
            Membership::where('id', $request->id)
                ->where('group_id', $request->group_id)
                ->update(array('confirmed' => $request->confirmed));

            $success = 'Invitation accepted';
        }

        if (!$request->confirmed && $request->user_id) {
            Membership::where('id', $request->id)
                ->where('user_id', auth()->id())
                ->delete();
            
            $success = 'Invitation declined';
            
            // Dispatch email to requester here
        }

        if (!$request->confirmed && $request->group_id) {
            Membership::where('id', $request->id)
                ->where('group_id', $request->group_id)
                ->delete();

            $success = 'Invitation declined';

            // Dispatch email to requester here
        }

        return $success;
    }
}