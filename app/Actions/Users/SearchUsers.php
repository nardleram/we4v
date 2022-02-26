<?php

namespace App\Actions\Users;

use App\Models\User;

class SearchUsers
{
    public function handle($string) : object
    {
        $str = trim($string);

        if (str_contains($str, ' ')) {
            $words = preg_split('/\s+/', $str, -1, PREG_SPLIT_NO_EMPTY);

            if (count($words) === 2) {
                $results = User::where('name', 'like', $words[0])
                    ->where('surname', 'like', $words[1])
                    ->orWhere('username', 'like', $string)
                    ->select([
                        'users.name as name',
                        'users.surname as surname',
                        'users.username as username',
                        'images.path as path',
                        'images.format as format'
                    ])
                    ->leftJoin('images', function ($join) {
                        $join->on('users.id', '=', 'imageable_id')
                        ->where('format', 'profile');
                    })
                    ->get();
            } elseif (count($words) > 2) { // Hmmm, not sure what to do here...
                $results = User::where('name', $words[0])
                    ->where('surname', $words[2])
                    ->orWhere('username', $string)
                    ->select([
                        'users.name as name',
                        'users.surname as surname',
                        'users.username as username',
                        'images.path as path',
                        'images.format as format'
                    ])
                    ->leftJoin('images', function ($join) {
                        $join->on('users.id', '=', 'imageable_id')
                        ->where('format', 'profile');
                    })
                    ->get();
            }
        } else {
            $results = User::where('username', 'like', '%'.lcfirst($str).'%')
                ->orWhere('username', 'like', ucfirst($str).'%')
                ->orWhere('name', 'like', ucfirst($str).'%')
                ->orWhere('name', 'like', '%'.lcfirst($str).'%')
                ->orWhere('surname', 'like', ucfirst($str).'%')
                ->orWhere('surname', 'like', '%'.lcfirst($str).'%')
                ->select([
                    'users.name as name',
                    'users.surname as surname',
                    'users.username as username',
                    'images.path as path',
                    'images.format as format'
                ])
                ->leftJoin('images', function ($join) {
                    $join->on('users.id', '=', 'imageable_id')
                    ->where('format', 'profile');
                })
                ->get();
        }

        return $results;
    }
}