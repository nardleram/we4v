<?php

namespace App\Actions\Votes;

use DateTime;
use App\Models\Vote;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetVotes
{
    use SoftDeletes;

    private $compileVotesArray;

    public function __construct(CompileVotesArray $compileVotesArray)
    {
        $this->compileVotesArray = $compileVotesArray;
    }
    
    public function handle($id, $operator) : array
    {
        $today = date("Y-m-d");
        $todayDate = new DateTime($today);
        
        $rawVotes = Vote::where('votes.owner', $id)
        ->where('votes.closing_date', $operator, $todayDate)
        ->leftJoin('vote_elements', function ($join) {
            $join->on('vote_elements.vote_id', '=', 'votes.id');
        })
        ->leftJoin('cast_votes', function ($join) {
            $join->on('cast_votes.vote_id', '=', 'votes.id');
        })
        ->leftJoin('groups', function ($join) {
            $join->on('groups.id', '=', 'votes.voteable_id');
        })
        ->leftJoin('teams', function ($join) {
            $join->on('teams.id', '=', 'votes.voteable_id');
        })
        ->leftJoin('memberships', function ($join) {
            $join->on('memberships.membershipable_id', '=', 'votes.voteable_id')
            ->where('memberships.deleted_at', null);
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'memberships.user_id')
            ->orOn('users.id', '=', 'votes.owner')
            ->orOn('users.id', '=', 'cast_votes.user_id');
        })
        ->select([
            'votes.id as vote_id',
            'votes.title as vote_title',
            'votes.closing_date as closing_date',
            'vote_elements.title as element_title',
            'vote_elements.id as element_id',
            'cast_votes.vote_id as cast_vote_id',
            'cast_votes.user_id as cast_vote_user_id',
            'cast_votes.element_id as cast_vote_element_id',
            'groups.name as group_name',
            'teams.name as team_name',
            'teams.group_id as team_group_id',
            'users.username as username',
            'users.id as user_id',
        ])
        ->groupBy([
            'votes.id',
            'vote_elements.id',
            'cast_votes.id',
            'groups.id',
            'teams.id',
            'cast_votes.user_id',
            'users.username',
            'users.id',
        ])
        ->orderBy('votes.title', 'asc')
        ->orderBy('vote_elements.title', 'asc')
        ->orderBy('groups.name', 'asc')
        ->orderBy('teams.name', 'asc')
        ->orderBy('users.username', 'asc')
        ->get();

        return $this->compileVotesArray->compileVotes($rawVotes);
    }
}