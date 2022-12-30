<?php

namespace App\Http\Controllers;

use App\Actions\Votes\StoreCastVote;
use App\Http\Requests\CastVoteRequest;

class CastVoteController extends Controller
{
    public function __construct(private StoreCastVote $storeCastVote)
    {}

    public function store(CastVoteRequest $request) : object
    {
        $this->storeCastVote->handle($request);

        return redirect()->back()->with([
            'flash' => ['message' => 'Your vote was successfully cast']
        ]);
    }
}
