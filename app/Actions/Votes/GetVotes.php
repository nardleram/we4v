<?php

namespace App\Actions\Votes;

use Carbon\Carbon;
use App\Models\Vote;

class GetVotes
{
    public function handle($id) : array
    {
        $rawVotes = Vote::where('votes.owner', $id)
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
            $join->on('memberships.membershipable_id', '=', 'votes.voteable_id');
        })
        ->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'memberships.user_id');
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

        // Assemble $votes array
        $currentVoteId = 0;
        $loop = 0;
        $votes = [];
        $voters = [];
        $allCastVoteEls = [];
        $voteCount = 0;
        $elementCount = 0;
        $voteElCount = 0;
        $voteElementVotedFor = false;
        $usersVoted = [];
        $currentGroupTeamName = '';
        $currentUsername = '';
        $currentElement = '';
        $numVotesCast = 0;

        foreach($rawVotes as $rawVote) {
            if ($rawVote->cast_vote_user_id) {
                array_push($allCastVoteEls, ['vote_id' => $rawVote->vote_id, 'element_id' => $rawVote->cast_vote_element_id, 'user_id' => $rawVote->cast_vote_user_id]);
            }
        }

        $castVoteEls = array_unique($allCastVoteEls, SORT_REGULAR);
        
        foreach($rawVotes as $rawVote) {
            // Compile vote data only once
            if ($currentVoteId !== $rawVote->vote_id) {
                if ($loop > 0) {
                    $numVotesCast = 0;
                    $elementCount = 0;
                    $currentUsername = '';
                    $voters = [];
                    ++$voteCount;
                }

                $votes[$voteCount]['vote_title'] = $rawVote->vote_title;
                $votes[$voteCount]['closing_date'] = Carbon::parse($rawVote->closing_date)->format('d M y');

                foreach ($castVoteEls as $castVoteEl) {
                    if ($rawVote->vote_id === $castVoteEl['vote_id']) {
                        $votes[$voteCount]['num_votes_cast'] = ++$numVotesCast;
                    }
                }

                if (!($currentGroupTeamName === $rawVote->group_name || $currentGroupTeamName === $rawVote->team_name)) {
                
                    if ($rawVote->group_name) {
                        $votes[$voteCount]['group_team_name'] = $rawVote->group_name;
                        $votes[$voteCount]['type'] = 'group';
                        $currentGroupTeamName = $rawVote->group_name;
                    } else {
                        $votes[$voteCount]['group_team_name'] = $rawVote->team_name;
                        $votes[$voteCount]['type'] = 'team';
                        $currentGroupTeamName = $rawVote->team_name;
                    }
                }
            }

            // Voters
            if ($currentUsername !== $rawVote->username) {
                if (!in_array($rawVote->username, $voters)) {
                    array_push($voters, $rawVote->username);
                }

                $votes[$voteCount]['voters'] = $voters;
            }

            // Vote elements
            foreach ($castVoteEls as $castVoteEl) {
                if ( 
                        $rawVote->cast_vote_element_id === $castVoteEl['element_id'] 
                        && $rawVote->cast_vote_user_id === $castVoteEl['user_id'] 
                        && (!in_array($rawVote->cast_vote_user_id, $usersVoted))
                        && $rawVote->element_id === $castVoteEl['element_id']
                        && $rawVote->user_id === $castVoteEl['user_id']
                    ) {
                    $votes[$voteCount]['elements'][$elementCount]['element_title'] = $rawVote->element_title;
                    $votes[$voteCount]['elements'][$elementCount]['numElVotes'] = ++$voteElCount;
                    array_push($usersVoted, $rawVote->cast_vote_user_id);
                }

                if ($rawVote->element_id === $castVoteEl['element_id']) {
                    $voteElementVotedFor = true;
                }
            }

            if ((!$voteElementVotedFor) && $rawVote->element_id !== $rawVote->cast_vote_element_id) {
                $voteElCount = 0;
                $votes[$voteCount]['elements'][$elementCount]['element_title'] = $rawVote->element_title;
                $votes[$voteCount]['elements'][$elementCount]['numElVotes'] = 0;
                $voteElementVotedFor = true;
            }

            if ($rawVote->element_title !== $currentElement) {
                if ($voteElementVotedFor) {
                    ++$elementCount;
                }
                $voteElementVotedFor = false;
                $usersVoted = [];
                $voteElCount = 0;
            }

            ++$loop;
            $currentVoteId = $rawVote->vote_id;
            $currentUsername = $rawVote->username;
            $currentElement = $rawVote->element_title;
        }
        
        return $votes;
    }
}