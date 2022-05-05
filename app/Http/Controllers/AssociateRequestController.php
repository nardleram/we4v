<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\ThrottleMail;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\AssociateRequestRequest;
use App\Mail\AssociationRequested;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssociateRequestController extends Controller
{
    public function store(AssociateRequestRequest $request)
    {
        try {
            User::findOrFail($request->requested_of)
                ->associations()
                ->attach($request->requested_by, ['created_at' => now(), 'updated_at' => now()]);

            $requestee = User::where('id', $request->requested_of)->first();
            $requester = User::where('id', $request->requested_by)->first();
            
            ThrottleMail::dispatch(new AssociationRequested($requestee, $requester), $requestee);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException();
        }

        return redirect()->back();
    }
}
