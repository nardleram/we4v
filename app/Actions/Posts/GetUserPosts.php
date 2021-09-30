<?php

namespace App\Actions\Posts;

use App\Models\User;
use App\Models\Image;
use App\Actions\Users\GetUsers;
use App\Actions\Posts\GetTalkboardPosts;

class GetUserPosts
{
    public function handlePosts(User $user, GetTalkboardPosts $getPostData, GetUsers $getUserData) : array
    {
        $rawPosts = $getPostData->getPosts([$user->id]);

        $user_ids = [$user->id];
        foreach ($rawPosts as $post) {
            array_push($user_ids, $post->comment_posted_by);
        }
        $ids = array_unique($user_ids);
        $users = $getUserData->getUsers($ids);

        return $getPostData->compilePostsArray($rawPosts, $users);
    }

    public function handleUser(User $user) : object
    {
        $user_bkgrnd_image = Image::get_user_bkgrnd_image($user);
        $user_profile_image = Image::get_user_profile_image($user);
        
        count($user_bkgrnd_image) > 0
            ? $user['bkgrnd_image'] = $user_bkgrnd_image[0]
            : $user['bkgrnd_image'] = 'images/bkgrnd.jpg';
        count($user_profile_image) > 0
            ? $user['profile_image'] = $user_profile_image[0]
            : $user['profile_image'] = 'images/nobody.png';

        return $user;
    }
}