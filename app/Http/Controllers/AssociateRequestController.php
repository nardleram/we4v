<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exceptions\UserNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class AssociateRequestController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'requested_of' => 'required',
            'requested_by' => 'required',
        ]);

        try {
            User::findOrFail($data['requested_of'])
                ->associations()
                ->attach($data['requested_by'], ['created_at' => now(), 'updated_at' => now()]);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException();
        }

        return Redirect::back();
    }
}
