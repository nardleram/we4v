<?php

namespace App\Actions\Groups;

use App\Models\Group;

class SearchGroups
{
    public function handle($string) : object
    {
        $str = trim($string);

        return Group::where('owner', '!=', auth()->id())
            ->where(function ($query) use ($str) {
                return $query
                    ->where('name', $str)
                    ->orWhere('name', 'like', ucfirst($str).'%')
                    ->orWhere('name', 'like', lcfirst($str).'%')
                    ->orWhere('name', 'like', '%'.ucfirst($str).'%')
                    ->orWhere('name', 'like', '%'.lcfirst($str).'%')
                    ->orWhere('geog_area', 'like', '%'.$str.'%');
            })
            ->get(['name', 'description', 'geog_area', 'owner']);
    }
}