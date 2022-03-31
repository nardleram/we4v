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
                    ->where('groups.name', $str)
                    ->orWhere('groups.name', 'like', ucfirst($str).'%')
                    ->orWhere('groups.name', 'like', lcfirst($str).'%')
                    ->orWhere('groups.name', 'like', '%'.ucfirst($str).'%')
                    ->orWhere('groups.name', 'like', '%'.lcfirst($str).'%')
                    ->orWhere('geog_area', 'like', '%'.$str.'%');
            })
            ->join('users', function ($join) {
                $join->on('groups.owner', '=', 'users.id');
            })
            ->select([
                'groups.id AS id',
                'groups.name AS name', 
                'groups.description AS description', 
                'groups.geog_area as geog_area', 
                'users.username AS owner'
            ])
            ->get();
    }
}