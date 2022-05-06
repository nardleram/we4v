<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class UpdateMembershipRequestResponse
{
    public function handle($request) : string
    {
        $success = '';

        if ($request->confirmed && $request->user_id) {
            $membership = Membership::where('id', $request->id)
                ->update(['confirmed' => true]);

            $membership 
            ? $success = 'Invitation accepted'
            : $success = 'Oops, something went wrong! Please refresh the page and try again.';
        }

        if ($request->confirmed && $request->group_id) {
            $membership = Membership::where('id', $request->id)
                ->update(['confirmed' => true]);

            $membership 
            ? $success = 'Invitation accepted'
            : $success = 'Oops, something went wrong! Please refresh the page and try again.';
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