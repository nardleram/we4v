<?php

namespace App\Actions\Users;

use App\Models\User;

class GetUsers
{
    public function getUsers($ids) : object
    {
        return User::select('id as user_id')
            ->whereIn('users.id', $ids)
            ->leftJoin('images', function ($join) {
                $join->on('users.id', '=', 'images.imageable_id')
                    ->where('images.format', '=', 'profile')
                    ->where('images.imageable_type', '=', 'App\Models\User');
            }) 
            ->select('users.id as user_id', 'name', 'surname', 'username', 'slug', 'images.path as path')
            ->get();
    }
}