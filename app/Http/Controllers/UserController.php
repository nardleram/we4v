<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Redirect;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\UpdatePasswordRequest;

class UserController extends Controller
{   
    public function show(User $user) : object
    {
        if ($user->id !== auth()->id()) {
            return view('errors.unauthorized');
        }
        
        try {
            $user = User::find(auth()->id());
        } catch (UserNotFoundException $exception) {
            return view('users.notFound', ['error' => $exception->getMessage()]);
        }

        return Inertia::render('MyProfile', [
            'user' => $user
        ]);
    }

    public function update(User $user, ProfileRequest $request)
    {
        $user->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'username' => $request->username,
            'email' => $request->email
        ]);

        return Redirect::route('myprofile', ['user' => $user]);
    }

    public function updatePassword(User $user, UpdatePasswordRequest $request) 
    {
        $password = Hash::make($request->newPassword);
        
        $user->update([
            'password' => $password
        ]);

        return Redirect::route('myprofile', ['user' => $user]);
    }
}
