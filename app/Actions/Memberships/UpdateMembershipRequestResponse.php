<?php

namespace App\Actions\Memberships;

use App\Jobs\ThrottleMail;
use App\Models\Membership;
use App\Mail\MembershipAccepted;
use App\Mail\MembershipRejected;
use Illuminate\Support\Facades\Mail;

class UpdateMembershipRequestResponse
{
    public function handle($request) : string
    {
        $success = '';
        $type = 'team';

        if ($request->confirmed && $request->user_id) {
            $membership = Membership::where('id', $request->id)
                ->update(['confirmed' => true]);

            $membership = Membership::where('id', $request->id)->first();

            $membership->membershipable_type === 'App\\Models\\Group'
            ? $type = 'group'
            : null;

            if ($membership) {
                $success = 'Invitation accepted';
                ThrottleMail::dispatch(new MembershipAccepted($membership, $type), $membership->updatedBy);
            } else {
                $success = 'Oops, something went wrong! Please refresh the page and try again.';
            }
        }

        if ($request->confirmed && $request->group_id) {
            $membership = Membership::where('id', $request->id)
                ->update(['confirmed' => true]);

            $membership 
            ? $success = 'Invitation accepted'
            : $success = 'Oops, something went wrong! Please refresh the page and try again.';
        }

        if (!$request->confirmed && $request->user_id) {
            $type = 'team';

            $membership = Membership::where('id', $request->id)->first();

            $membership->membershipable_type === 'App\\Models\\Group'
            ? $type = 'group'
            : null;

            Mail::to($membership->updatedBy)->send(
                new MembershipRejected($membership, $type)
            );

            Membership::where('id', $request->id)->delete();
            
            $success = 'Invitation declined';   
        }

        if (!$request->confirmed && $request->group_id) {
            // Dispatch email to requester here

            Membership::where('id', $request->id)
                ->where('group_id', $request->group_id)
                ->delete();

            $success = 'Invitation declined';
        }

        return $success;
    }
}