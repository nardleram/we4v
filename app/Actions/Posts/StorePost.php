<?php

namespace App\Actions\Posts;

use App\Models\Post;
use App\Models\Image;
use Illuminate\Http\Request;

class StorePost
{
    public function handle(Request $request) : object
    {
        $post = Post::create([
            'body' => $request->body,
            'user_id' => $request->user_id
        ]);

        $post['posted_at'] = $post->created_at->diffForHumans();

        if (isset($request['image'])) {
            $image = $request['image']->store('images/posts', 'public');

            $post['image'] = $image;

            Image::create([
                'imageable_id' => $post->id,
                'imageable_type' => 'App\Models\Post',
                'path' => $image,
                'format' => 'post',
            ]);
        }

        return $post;
    }
}