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
    public function __construct(GetUserPosts $getUserPosts, GetTalkboardPosts $getPosts)
    {
        $this->getUserPosts = $getUserPosts;
        $this->getPosts = $getPosts;
    }

    public function index() : object
    {
        $posts = $this->getPosts->handle();

        return Inertia::render('Talkboard', [
            'posts' => $posts,
            'posts_status' => 'success',
        ]);
    }

    public function show(User $user) : object
    {
        $posts = $this->getUserPosts->handlePosts($user);

        $user = $this->getUserPosts->handleUser($user);

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