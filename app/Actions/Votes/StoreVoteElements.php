<?php

namespace App\Actions\Votes;

use App\Models\VoteElement;
use Illuminate\Http\Request;

class StoreVoteElements
{
    public function handle(Request $request, $id) : void
    {
        foreach ($request->vote_elements as $element) {
            VoteElement::create([
                'title' => $element,
                'vote_id' => $id,
            ]);
        }
    }
}