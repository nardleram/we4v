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

    protected $fillable = ['title', 'voteable_id', 'voteable_type', 'owner', 'closing_date'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    protected $rawVotes;

    public function voteElements()
    {
        $this->hasMany(VoteElement::class);
    }

    public function castVotes()
    {
        return $this->hasManyThrough(
            CastVote::class, 
            VoteElement::class,
            'vote_id',
            'element_id'
        );
    }

    public static function getPendingVotes($ids) : array
    {
        $rawVotes = Vote::whereIn('votes.voteable_id', $ids)
        ->orWhere('votes.owner', auth()->id())
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
        $votes = [];
        $voted = [];
        $elements = [];
        $keyedElements = [];

        foreach($rawVotes as $rawVote) {
            if ($rawVote->cast_vote_user_id === auth()->id()) {
                array_push($voted, $rawVote->vote_id);
            }
        }
        $votedUnique = array_unique($voted);

        foreach($rawVotes as $rawVote) {
            // Ignore votes cast by authUser
            if ( (!$rawVote->cast_vote_user_id) || (!in_array($rawVote->vote_id, $votedUnique)) ) {

                // Compile vote data only once
                if ($currentVoteId !== $rawVote->vote_id) {
                    if ($loop > 0) {
                        $elements = [];
                        $keyedElements = [];
                        ++$voteCount;
                    }
    
                    $votes[$voteCount]['vote_id'] = $rawVote->vote_id;
                    $votes[$voteCount]['vote_title'] = $rawVote->vote_title;
                    $votes[$voteCount]['closing_date'] = Carbon::parse($rawVote->closing_date)->format('d M y');
                    $votes[$voteCount]['vote_owner'] = $rawVote->vote_owner;
                }
                
                if (!in_array($rawVote->element_title, $elements)) {
                    array_push($elements, $rawVote->element_title);
                    array_push($keyedElements, ['element_title' => $rawVote->element_title, 'element_id' => $rawVote->vote_el_id]);
                }
                
            }
            if (count($elements) > 0) {
                $votes[$voteCount]['elements'] = $keyedElements;
            }

            ++$loop;
            $currentVoteId = $rawVote->vote_id;
        }
        
        return $votes;
    }
}
