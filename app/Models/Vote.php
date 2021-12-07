<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['title', 'voteable_id', 'voteable_type', 'owner'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    protected $rawVotes;

    protected $casts = ['closing_date' => 'datetime:d M Y'];

    public function getClosingDateAttribute($date)
    {
        return Carbon::parse($date)->format('d M Y');
    }

    public static function getPendingVotes($ids) : array
    {
        $rawVotes = Vote::whereIn('voteable_id', $ids)
        ->leftJoin('vote_elements', function ($join) {
            $join->on('votes.id', '=', 'vote_elements.vote_id');
        })
        ->leftJoin('cast_votes', function ($join) {
            $join->on('cast_votes.vote_id', '=', 'votes.id');
        })
        ->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'votes.owner');
        })
        ->select([
            'votes.id as vote_id',
            'votes.title as vote_title',
            'votes.closing_date as closing_date',
            'users.username as vote_owner',
            'vote_elements.id as vote_el_id',
            'vote_elements.title as element_title',
            'cast_votes.user_id as cast_vote_user_id',
            'cast_votes.element_id as cast_vote_element_id',
        ])
        ->groupBy([
            'votes.id',
            'vote_elements.title',
            'vote_elements.id',
            'cast_votes.user_id',
            'users.username',
            'cast_votes.id',
        ])
        ->orderBy('votes.title')
        ->get();

        // Assemble $votes array
        $currentVoteId = 0;
        $loop = 0;
        $voteCount = 0;
        $elementCount = 0;
        $currentElement = '';
        $votes = [];
        $voted = [];

        foreach($rawVotes as $rawVote) {
            if ($rawVote->cast_vote_user_id === auth()->id()) {
                array_push($voted, $rawVote->vote_id);
            }
        }
        $votedUnique = array_unique($voted);

        foreach($rawVotes as $rawVote) {
            //Strip out votes cast by authUser
            if ( (!$rawVote->cast_vote_user_id) || (!in_array($rawVote->vote_id, $votedUnique)) ) {

                // Compile vote data only once
                if ($currentVoteId !== $rawVote->vote_id) {
                    if ($loop > 0) {
                        $elementCount = 0;
                        ++$voteCount;
                    }
    
                    $votes[$voteCount]['vote_id'] = $rawVote->vote_id;
                    $votes[$voteCount]['vote_title'] = $rawVote->vote_title;
                    $votes[$voteCount]['closing_date'] = Carbon::parse($rawVote->closing_date)->format('d M y');
                    $votes[$voteCount]['vote_owner'] = $rawVote->vote_owner;
                }
    
                if ($currentElement !== $rawVote->element_title) {
                    $votes[$voteCount]['elements'][$elementCount]['element_title'] = $rawVote->element_title;
                    $votes[$voteCount]['elements'][$elementCount]['element_id'] = $rawVote->vote_el_id;
                    ++$elementCount;
                }
            }

            ++$loop;
            $currentVoteId = $rawVote->vote_id;
            $currentElement = $rawVote->element_title;
        }
        
        return $votes;

    }
}
