<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Votes\GetVotes;
use App\Actions\Votes\StoreVote;
use App\Actions\Groups\GetGroups;
use App\Actions\Votes\UpdateVote;
use App\Http\Requests\StoreVoteRequest;
use App\Actions\Votes\StoreVoteElements;
use App\Http\Requests\UpdateVoteRequest;

class VoteController extends Controller
{
    public function __construct(
        private GetVotes $getVotes, 
        private StoreVote $storeVote, 
        private UpdateVote $updateVote, 
        private StoreVoteElements $storeVoteElements, 
        private GetGroups $getGroups
    ) {}
    
    public function index() : object
    {
        return Inertia::render('MyVotes', [
            'myvotes' => $this->getVotes->handle(auth()->id(), '>='),
            'myclosedvotes' => $this->getVotes->handle(auth()->id(), '<='),
            'mygroups' => $this->getGroups->handle(auth()->id())
        ]);
    }

    public function store(StoreVoteRequest $request)
    {
        $vote = $this->storeVote->handle($request);

        $vote
        ? $this->storeVoteElements->handle($request, $vote->id)
        : null;

        return redirect()->back()->with([
            'myvotes' => $this->getVotes->handle(auth()->id(), '>='),
            'myclosedvotes' => $this->getVotes->handle(auth()->id(), '<='),
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Vote created']
        ]);
    }

    public function update(UpdateVoteRequest $request)
    {
        $this->updateVote->handle($request);

        return redirect()->back()->with([
            'flash' => ['message' => 'Vote deadline extended']
        ]);
    }
}
