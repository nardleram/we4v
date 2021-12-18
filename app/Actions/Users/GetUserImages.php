<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Models\Image;

class GetUserImages
{
    public function handle(User $user) : object
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