<?php

namespace App\Actions\Teams;

use App\Models\Team;
use Illuminate\Http\Request;

class UpdateTeam
{
    public function handle(Request $request) : void
    {
        Team::where('id', $request->membershipable_id)
            ->update([
                'function' => $request->function,
                'name' => $request->name
            ]);
    }
}