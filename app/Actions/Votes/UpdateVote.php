<?php

namespace App\Actions\Votes;

use App\Models\Vote;
use Illuminate\Http\Request;

class UpdateVote
{
    public function handle(Request $request) : void
    {
        Vote::where('id', $request->id)
        ->update([
            'closing_date' => $request->closing_date,
        ]);
    }
}