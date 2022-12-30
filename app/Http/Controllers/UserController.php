<?php

namespace App\Http\Controllers;

use App\Actions\Articles\GetArticles;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Actions\Users\SearchUsers;
use App\Actions\Posts\GetUserPosts;
use App\Actions\Users\GetUserImages;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\NotFoundException;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\SearchUserRequest;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\UpdatePasswordRequest;

class UserController extends Controller
{
    public function __construct(
        private GetUserPosts $getUserPosts,
        private GetUserImages $getUserImages,
        private SearchUsers $searchUsers,
        private GetArticles $getArticles
    ) {}
    
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
        return Inertia::render('UserShow', [
            'posts' => $this->getUserPosts->handle($user),
            'posts_status' => 'success',
            'user' => $this->getUserImages->handle($user),
            'myArticles' => $this->getArticles->handle($user->id)
        ]);
    }

    public function update(User $user, ProfileRequest $request)
    {
        $user->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'username' => $request->username,
            'slug' => Str::slug($request->username),
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

    public function search(SearchUserRequest $string)
    {
        return redirect()->back()->with([
            'searchResults' => [session(['searchData' => $this->searchUsers->handle($string->searchString)])]
        ]);
    }
}
