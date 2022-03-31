<?php

namespace App\Actions\Groups;

use App\Models\Group;
use Illuminate\Http\Request;

class UpdateGroup
{
    public function handle(Request $request) : int
    {
        return Group::where('id', $request->membershipable_id)
            ->update([
                'description' => $request->description,
                'geog_area' => $request->geog_area,
                'name' => $request->name
            ]);
    }
}