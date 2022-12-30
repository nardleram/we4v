<?php

namespace App\Http\Controllers;

use App\Actions\Memberships\UpdateMembershipRequestResponse;
use App\Http\Requests\UpdateMembershipRequestResponseRequest;

class MembershipRequestResponseController extends Controller 
{
    public function __construct(private UpdateMembershipRequestResponse $updateMembershipRequestResponse)
    {}

    public function update(UpdateMembershipRequestResponseRequest $request) : object
    {
        $flashMessage = $this->updateMembershipRequestResponse->handle($request);

        return redirect()->back()->with(['flash' => ['message' => $flashMessage]]);
    }
}
