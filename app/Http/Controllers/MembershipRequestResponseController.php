<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Groups\GetGroups;
use App\Actions\Memberships\StoreMembershipRequestResponses;
use App\Http\Requests\StoreMembershipRequestResponseRequest;

class MembershipRequestResponseController extends Controller 
{
    public function store(StoreMembershipRequestResponseRequest $request, StoreMembershipRequestResponses $action, GetGroups $getGroups) : object
    {
        $user = $action->handle($request);

        return Inertia::render('MyGroups', [
            'mygroups' => $getGroups->handle(auth()->id()),
            'user' => $user
        ]);
    }
}
