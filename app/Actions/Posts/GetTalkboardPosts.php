<?php

namespace App\Actions\Posts;

use App\Models\Post;
use App\Models\User;
use App\Models\Association;

class GetTalkboardPosts
{
    public function handle() : array
    {
        $assocIds = Association::getAssociations();

        $rawPosts = Post::getAllPostData($assocIds);

        $commentUserIds = [];
        foreach ($rawPosts as $rawPost) {
            array_push($commentUserIds, $rawPost->comment_posted_by);
        }
        $allUserIds = array_merge($assocIds, $commentUserIds);
        $userIds = array_unique($allUserIds);

        $users = User::getUsersData($userIds);

        return Post::compilePostsArray($rawPosts, $users);
    }
}