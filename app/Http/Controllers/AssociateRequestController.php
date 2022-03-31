<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\AssociateRequestRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssociateRequestController extends Controller
{
    public function store(AssociateRequestRequest $request)
    {
        try {
            User::findOrFail($request->requested_of)
                ->associations()
                ->attach($request->requested_by, ['created_at' => now(), 'updated_at' => now()]);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException();
        }

        return redirect()->back();
    }
}
