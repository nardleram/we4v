<?php

namespace App\Actions\Votes;

use DateTime;
use Carbon\Carbon;
use App\Models\Vote;
use Illuminate\Database\Eloquent\SoftDeletes;

class GetVotes
{
    use SoftDeletes;
    
    public function handle($id, $operator) : array
    {
        $today = date("Y-m-d");
        $todayDate = new DateTime($today);
        
        $rawVotes = Vote::where('votes.owner', $id)
        ->where('votes.closing_date', $operator, $todayDate)
        // ->whereIn('votes.id', ['f7cd6f61-1934-41e0-85fc-040f5cc59964'])
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

        // Assemble $votes array
        $currentVoteId = 0;
        $loop = 0;
        $voteCount = 0;
        $elementCount = 0;
        $voteElCount = 0;
        $voteElementVotedFor = false;
        $currentGroupTeamName = '';
        $currentUsername = '';
        $currentElement = '';
        $numVotesCast = 0;
        $votes = [];
        $voters = [];
        $allCastVoteEls = [];
        $usersVoted = [];
        $votedUsers = [];

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
                    $voteElementVotedFor = false;
                    $votedUsers = [];
                }

                $votes[$voteCount]['vote_title'] = $rawVote->vote_title;
                $votes[$voteCount]['closing_date'] = Carbon::parse($rawVote->closing_date)->format('d M y');
                $votes[$voteCount]['input_closing_date'] = $rawVote->closing_date;

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

            if ($rawVote->element_title !== $currentElement) {
                if ($voteElementVotedFor) {
                    ++$elementCount;
                }
                $voteElementVotedFor = false;
                $usersVoted = [];
                $voteElCount = 0;
            }

            // Voters
            if ($currentUsername !== $rawVote->username) {
                if (!in_array($rawVote->username, $voters)) {
                    array_push($voters, $rawVote->username);
                }

                $votes[$voteCount]['voters'] = $voters;
            }

            // Log users who have voted, but without their vote decision
            if ( $rawVote->cast_vote_user_id && ($rawVote->cast_vote_user_id === $rawVote->user_id) ) {
                if (!in_array($rawVote->username, $votedUsers)) {
                    array_push($votedUsers, $rawVote->username);
                }

                $votes[$voteCount]['users_who_voted'] = $votedUsers;
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
            
            if ( (!$voteElementVotedFor) && ($rawVote->element_id !== $rawVote->cast_vote_element_id) && ($rawVote->element_title !== $currentElement) ) {
                $voteElCount = 0;
                $votes[$voteCount]['elements'][$elementCount]['element_title'] = $rawVote->element_title;
                $votes[$voteCount]['elements'][$elementCount]['numElVotes'] = 0;
                $voteElementVotedFor = true;
            }

            ++$loop;
            $currentVoteId = $rawVote->vote_id;
            $currentUsername = $rawVote->username;
            $currentElement = $rawVote->element_title;
        }
        
        return $votes;
    }
}