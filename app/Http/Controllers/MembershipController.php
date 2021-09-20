<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use App\Actions\Memberships\StoreMemberships;
use App\Http\Requests\StoreMembershipRequest;

class MembershipController extends Controller
{
    public function store(StoreMembershipRequest $request, StoreMemberships $action)
    {
        $action->handle($request);

        return Redirect::route('MyTeams');
    }
}
