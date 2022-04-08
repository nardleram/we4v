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
        if (auth()->id()) {
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
            ->join('memberships', function ($join) {
                $join->on('memberships.user_id', '=', 'users.id');
            })
            ->select([
                'votes.id as vote_id',
                'votes.title as vote_title',
                'votes.closing_date as closing_date',
                'votes.created_at as start_date',
                'users.username as vote_owner',
                'memberships.created_at as member_join_date',
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
                'memberships.created_at'
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
                // Filter out users who became members after vote start date
                if ( (!$rawVote->cast_vote_user_id) || (!in_array($rawVote->vote_id, $votedUnique))
                    && ($rawVote->start_date >= $rawVote->member_join_date) 
                ) {

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

        return [];
    }

    public static function getClosedVotes() : array
    {
        $rawResults = Vote::where('votes.owner', '!=', auth()->id())
            ->join('memberships AS Me', function ($join) {
                $join->on('Me.membershipable_id', '=', 'votes.voteable_id')
                ->where('Me.user_id', auth()->id());
            })
            ->join('vote_elements AS Ve', function ($join) {
                $join->on('Ve.vote_id', '=', 'votes.id');
            })
            ->leftJoin('teams AS Te', function ($join) {
                $join->on('Te.id', '=', 'votes.voteable_id');
            })
            ->leftJoin('groups AS Gr', function ($join) {
                $join->on('Gr.id', '=', 'votes.voteable_id');
            })
            ->select([
                'votes.id AS vote_id',
                'votes.title AS vote_title',
                'votes.closing_date as closing_date',
                'Ve.id AS vote_element_id',
                'Ve.title AS vote_element_title',
                'Gr.name AS group_name',
                'Gr.id AS group_id',
                'Te.name AS team_name',
                'Te.id AS team_id'
            ])
            ->groupBy([
                'votes.id',
                'Ve.id',
                'Gr.name',
                'Gr.id',
                'Te.name',
                'Te.id',
                'Ve.title'
            ])
            ->orderBy('votes.title')
            ->orderBy('Ve.id')
            ->get();

        // Compile results array
        $voteCount = 0;
        $elementCount = 0;
        $currentVoteId = 0;
        $currentElementId = 0;
        $loop = 0;
        $voteResults = [];

        foreach ($rawResults as $result) {
            
            if ($loop > 0 && $currentVoteId !== $result->vote_id) {
                ++$voteCount;
                $elementCount = 0;
            }

            if ($loop = 0) {
                $currentElementId = $result->cast_element_id;
            }

            $voteResults[$voteCount]['vote_title'] = $result->vote_title;
            $voteResults[$voteCount]['group_name'] = $result->group_name;
            $voteResults[$voteCount]['team_name'] = $result->team_name;
            $voteResults[$voteCount]['close_date'] = Carbon::parse($result->closing_date)->format('d M y');

            // Count vote elements voted for
            if ($result->vote_element_id !== $currentElementId) {
                $voteResults[$voteCount]['results'][$elementCount]['element_title'] = $result->vote_element_title;
                $voteResults[$voteCount]['results'][$elementCount]['numElVotes'] = CastVote::where('element_id', $result->vote_element_id)->count();
                ++$elementCount;
            }

            $currentVoteId = $result->vote_id;
            $currentElementId = $result->vote_element_id;
            ++$loop;
        }
        
        return $voteResults;
    }
}
