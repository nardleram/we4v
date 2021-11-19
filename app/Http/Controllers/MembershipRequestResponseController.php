<?php

namespace App\Http\Controllers;

use App\Actions\Memberships\StoreMembershipRequestResponse;
use App\Http\Requests\StoreMembershipRequestResponseRequest;

class MembershipRequestResponseController extends Controller 
{
    private $storeMembershipRequestResponse;

    public function __construct(StoreMembershipRequestResponse $storeMembershipRequestResponse)
    {
        $this->storeMembershipRequestResponse = $storeMembershipRequestResponse;
    }

    public function store(StoreMembershipRequestResponseRequest $request) : object
    {
        $flashMessage = $this->storeMembershipRequestResponse->handle($request);

        return redirect()->back()->with(['flash' => ['message' => $flashMessage]]);
    }
}
