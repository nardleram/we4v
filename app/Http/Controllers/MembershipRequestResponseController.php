<?php

namespace App\Http\Controllers;

use App\Actions\Memberships\UpdateMembershipRequestResponse;
use App\Http\Requests\UpdateMembershipRequestResponseRequest;

class MembershipRequestResponseController extends Controller 
{
    private $updateMembershipRequestResponse;

    public function __construct(UpdateMembershipRequestResponse $updateMembershipRequestResponse)
    {
        $this->updateMembershipRequestResponse = $updateMembershipRequestResponse;
    }

    public function update(UpdateMembershipRequestResponseRequest $request) : object
    {
        $flashMessage = $this->updateMembershipRequestResponse->handle($request);

        return redirect()->back()->with(['flash' => ['message' => $flashMessage]]);
    }
}
