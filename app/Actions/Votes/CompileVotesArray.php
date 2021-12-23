<?php

namespace App\Actions\Votes;

use Carbon\Carbon;

class CompileVotesArray
{
    public function compileVotes($rawVotes) : array 
    {
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