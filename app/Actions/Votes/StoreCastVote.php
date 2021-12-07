<?php

namespace App\Actions\Votes;

use App\Models\CastVote;
use Illuminate\Http\Request;

class StoreCastVote
{
    public function handle(Request $request) : void
    {
        CastVote::create([
            'element_id' => $request->element_id,
            'user_id' => $request->user_id,
            'vote_id' => $request->vote_id,
        ]);
    }
}