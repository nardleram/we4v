<?php

namespace App\Http\Controllers;

use App\Actions\Memberships\StoreMembership;
use App\Actions\Memberships\DeleteMembership;
use App\Http\Requests\StoreMembershipRequest;

class MembershipController extends Controller
{
    public function __construct(
        private StoreMembership $storeMembership, 
        private DeleteMembership $deleteMembership
    ) {}

    public function store(StoreMembershipRequest $request)
    {
        $this->storeMembership->handle($request);

        return redirect()->back()->with([
            'flash' => ['message' => 'Invitation sent.']
        ]);
    }

    public function destroy($id) : object
    {
        $this->deleteMembership->handle($id);

        return redirect()->back()->with([
            'flash' => ['message' => 'Membership deleted.']
        ]);
    }
}
