<?php

namespace App\Http\Controllers;

use App\Http\Resources\LikeCollection;
use App\Post;

class PostLikeController extends Controller
{
    public function store(Post $post)
    {
        $post->likes()->toggle(auth()->user()); // likes() returns relationship query itself, so you can tag on other methods: tag on other stuff before getting data. toggle() is like create, but binary in that the table column can be switched back and forth from liked (1) and not-liked (null).

        return new LikeCollection($post->likes); // likes returns (a collection of) records from db: data
    }
}
