<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Actions\Posts\GetUserPosts;
use App\Actions\Users\GetUserImages;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Redirect;
use App\Exceptions\NotFoundException;
use App\Http\Requests\UpdatePasswordRequest;

class UserController extends Controller
{
    private $getUserPosts;
    private $getUserArticles;
    private $getUserGroups;
    private $getUserProjects;
    private $getUserVotes;
    
    public function __construct(GetUserPosts $getUserPosts, GetUserImages $getUserImages)
    {
        $this->getUserPosts = $getUserPosts;
        $this->getUserImages = $getUserImages;
        // $getUserArticles
        // $getUserGroups
        // $getUserProjects
        // $getUserVotes
    }
    
    public function showProfile(User $user) : object
    {
        if ($user->id !== auth()->id()) {
            return view('errors.unauthorized');
        }
        
        try {
            $user = User::find(auth()->id());
        } catch (NotFoundException $exception) {
            return view('users.notFound', ['error' => $exception->getMessage()]);
        }

        return Inertia::render('MyProfile', [
            'user' => $user
        ]);
    }

    public function show(User $user) : object
    {
        $posts = $this->getUserPosts->handle($user);

        $user = $this->getUserImages->handle($user);

        return Inertia::render('Show', [
            'posts' => $posts,
            'posts_status' => 'success',
            'user' => $user,
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
