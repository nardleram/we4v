<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Posts\GetTalkboardPosts;
use App\Actions\Posts\StorePost;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    private $getPosts;
    
    public function __construct(GetTalkboardPosts $getPosts)
    {
        $this->getPosts = $getPosts;
    }

    public function index() : object
    {
        return Inertia::render('Talkboard', [
            'posts' => $this->getPosts->handle(),
            'posts_status' => 'success'
        ]);
    }

    public function store(PostRequest $request, StorePost $action)
    {
        $post = $action->handle($request);

        if ($post['image']) {
            return $post;
        } else {
            return redirect()->route('talkboard');
        }
    }
}