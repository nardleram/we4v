<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Actions\Posts\GetTalkboardPosts;
use App\Actions\Posts\GetUserPosts;
use App\Actions\Posts\StorePost;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Redirect;

class PostController extends Controller
{
    public function index(GetTalkboardPosts $action) : object
    {
        $posts = $action->handle();

        return Inertia::render('Talkboard', [
            'posts' => $posts,
            'posts_status' => 'success',
        ]);
    }

    public function show(User $user, GetUserPosts $action) : object
    {
        $posts = $action->handlePosts($user);

        $user = $action->handleUser($user);

        return Inertia::render('Show', [
            'posts' => $posts,
            'posts_status' => 'success',
            'user' => $user,
        ]);
    }

    public function store(PostRequest $request, StorePost $action) : object
    {
        $post = $action->handle($request);
        
        if (!$post['image']) {
            return Redirect::route('talkboard');
        } else {
            return $post;
        }
        
    }
}