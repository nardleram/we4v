<?php

namespace App\Actions\Votes;

use App\Models\Vote;
use Illuminate\Http\Request;

class StoreVote
{
    public function handle(Request $request) : object
    {
        return Vote::create([
            'title' => $request->title,
            'owner' => $request->owner,
            'voteable_id' => $request->voteable_id,
            'voteable_type' => $request->voteable_type,
        ]);
    }
}