<?php

namespace App\Http\Controllers;

use App\Associate;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;
use App\Post;
use Intervention\Image\Facades\Image as Image;

class PostController extends Controller
{
    public function index()
    {
        $associates = Associate::associations();

        $ids = [];
        foreach ($associates as $associate) {
            array_push($ids, $associate->user_id);
            array_push($ids, $associate->associate_id);
        }
        $my_ids = array_unique($ids);
        
        if ($associates->isEmpty()) {
            return new PostCollection(request()->user()->posts);
        } else {
            return new PostCollection(Post::whereIn('user_id', $my_ids)->get());
        }

        $posts = [
            'data' => [
                'data' => [
                    'type' => 'posts',
                    'post_id' => $this->id,
                    'attributes' => [
                        'posted_by' => 'user_object->user->uname',
                        'posted_by_profile_pic' => 'user_object->user->images->profile',
                        'posted_at' => $this->posted_at,
                        'body' => $this->body,
                        'image' => $this->image,
                        'data' => [
                            'type' => 'likes',
                            'like_id' => 'like_object->id',
                            'like_count' => 'like_count',
                            'attributes' => [

                            ]
                        ],
                        'data' => [
                            'type' => 'comments',
                            'comment_id' => $this->id,
                            'comment_count' => 'count',
                            'attributes' => []
                        ],
                    ]
                ]
            ]
        ];

    }
    
    public function store()
    {
        $data = request()->validate([
            'body' => 'required',
            'image' => '',
            'width' => '',
            'height' => '',
        ]);

        if (isset($data['image'])) {
            $image = $data['image']->store('post-images', 'public');

            Image::make($data['image'])
                ->fit($data['width'],$data['height'])
                ->save(storage_path('app/public/post-images/'.$data['image']->hashName()));
        }

        $post = request()->user()->posts()->create([
            'body' => $data['body'],
            'image' => $image ?? null,

        ]);
        
        return new PostResource($post);
    }
}
