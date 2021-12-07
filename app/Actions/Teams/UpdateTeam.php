<?php

namespace App\Actions\Teams;

use App\Models\Team;
use Illuminate\Http\Request;

class UpdateTeam
{
    public function handle(Request $request) : int
    {
        return Team::where('id', $request->membershipable_id)
            ->where('owner', $request->owner)
            ->update([
                'function' => $request->function,
                'name' => $request->name
            ]);
    }
}