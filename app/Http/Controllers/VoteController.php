<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Votes\GetVotes;
use App\Actions\Votes\StoreVote;
use App\Actions\Groups\GetGroups;
use App\Http\Requests\StoreVoteRequest;
use App\Actions\Votes\StoreVoteElements;

class VoteController extends Controller
{
    private $getVotes;
    private $storeVote;
    private $getGroups;
    private $storeVoteElements;

    public function __construct(GetVotes $getVotes, StoreVote $storeVote, StoreVoteElements $storeVoteElements, GetGroups $getGroups)
    {
        $this->storeVote = $storeVote;
        $this->getVotes = $getVotes;
        $this->getGroups = $getGroups;
        $this->storeVoteElements = $storeVoteElements;
    }
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

        $this->storeVoteElements->handle($request, $vote->id);

        return redirect()->back()->with([
            'myvotes' => $this->getVotes->handle(auth()->id(), '>='),
            'myclosedvotes' => $this->getVotes->handle(auth()->id(), '<='),
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Vote created']]);
    }
}
