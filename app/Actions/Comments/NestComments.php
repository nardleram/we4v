<?php

namespace App\Actions\Comments;

use Carbon\Carbon;

class NestComments
{
    public function handle($comments) : array
    {
        $nestedComments = [];
        $comment = false;
        $loop = 0;

        if (count($comments) > 0) {
            $thisComment = $comments->first();

            $thisComment['created'] = Carbon::parse($thisComment['created_at'])->format('j M Y, H:i');

            array_push($nestedComments, $thisComment);

            $remainingComments = $comments->where('id', '!=', $thisComment->id);

            while (count($nestedComments) < $comments->count()) {
                //$thisComment has a child in $remainingComments?
                $nextComment = $remainingComments->first(function ($item) use ($thisComment) {
                    return ($item->parent_id === $thisComment->id);
                });
                
                //$thisComment has sibling with same parent comment?
                if (!$nextComment) {
                    $nextComment = $remainingComments->first(function ($item) use ($thisComment) {
                        return (($item->parent_id === $thisComment->parent_id)
                        && ($item->parent_type === 'App\Models\Comment'));
                    });
                }
                
                //Must be a reponse to parent article
                if (!$nextComment) {
                    $nextComment = $remainingComments->first(function ($item) {
                        return $item->parent_type === 'App\Models\Article';
                    });
                }

                if ($nextComment && !$comment) { 
                    $comment = $comments->first(function ($item) use ($nextComment) {
                        return $item->id === $nextComment->parent_id;
                    });
                }
                
                if ($nextComment && $comment) {
                    $nextComment['reply_to'] = $comment['comment_author'];
                    $nextComment['parent_created_at'] = Carbon::parse($comment['created_at'])->format('j M Y, H:i');
                }

                if ($nextComment && count($nestedComments) < count($comments)) {
                    $nextComment['created'] = Carbon::parse($nextComment['created_at'])->format('j M Y, H:i');

                    $nextComment->approval_user_id === auth()->id() ? $nextComment['user_approves'] = true : $nextComment['user_approves'] = false;
                    
                    array_push($nestedComments, $nextComment);

                    $remainingComments = $remainingComments->filter(function ($item) use ($nextComment) {
                        return $item->id !== $nextComment->id;
                    });
                }
                
                $thisComment = $nextComment;
                $nextComment = null;
                $comment = null;
                ++$loop;
            }
        }
        
        return $nestedComments;
    }
}