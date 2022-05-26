<?php

namespace App\Actions\Posts;

use App\Models\User;
use App\Actions\Users\GetUsers;
use App\Actions\Posts\GetTalkboardPosts;

class GetUserPosts
{
    private $getPostData;
    private $getUserData;
    
    public function __construct(GetTalkboardPosts $getPostData, GetUsers $getUserData)
    {
        $this->getPostData = $getPostData;
        $this->getUserData = $getUserData;
    }

    public function handle(User $user) : array
    {
        $rawPosts = $this->getPostData->getPosts([$user->id]);

        $user_ids = [$user->id];
        foreach ($rawPosts as $post) {
            array_push($user_ids, $post->comment_posted_by);
        }
        $ids = array_unique($user_ids);
        $users = $this->getUserData->getUsers($ids);

        return $this->getPostData->compilePostsArray($rawPosts, $users);
    }
}