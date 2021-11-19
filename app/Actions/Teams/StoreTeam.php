<?php

namespace App\Actions\Teams;

use App\Models\Team;
use Illuminate\Http\Request;

class StoreTeam
{
    public function handle(Request $request) : object
    {
        return Team::create([
            'name' => $request->name,
            'function' => $request->function,
            'owner' => $request->owner,
            'group_id' => $request->group_id
        ]);
    }
}