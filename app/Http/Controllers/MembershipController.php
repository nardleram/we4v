<?php

namespace App\Http\Controllers;

use App\Actions\Memberships\StoreMemberships;
use App\Http\Requests\StoreMembershipRequest;

// Can delete this entire file methinks

class MembershipController extends Controller
{
    public function store(StoreMembershipRequest $request, StoreMemberships $action)
    {
        $action->handle($request, 27);

        return redirect()->back()->with([
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Invitation accepted.']
        ]);
    }
}
