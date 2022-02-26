<?php

namespace App\Actions\Groups;

use App\Models\Group;
use Illuminate\Http\Request;

class StoreGroup
{
    public function handle(Request $request) : object
    {
        return Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner' => $request->owner,
            'geog_area' => $request->geog_area
        ]);
    }
}