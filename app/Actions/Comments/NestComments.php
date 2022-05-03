<?php

namespace App\Actions\Comments;

use Carbon\Carbon;

class NestComments
{
    public function handle($comments) : array
    {
        $nestedComments = [];
        $nestedCommentsCollection = collect();
        $comment = false;

        if (count($comments) > 0) {
            $thisComment = $comments->first();

            $thisComment['created'] = Carbon::parse($thisComment['created_at'])->format('j M Y, H:i');

            array_push($nestedComments, $thisComment);

            $remainingComments = $comments->where('id', '!=', $thisComment->id);

            // Determine which should be $nextComment to display...
            while (count($nestedComments) < $comments->count()) {
                // Does $thisComment have a child (reply) in $remainingComments?
                $nextComment = $remainingComments->first(function ($item) use ($thisComment) {
                    return ($item->parent_id === $thisComment->id);
                });
                
                // Does $thisComment have sibling (same parent_id)?
                if (!$nextComment) {
                    $nextComment = $remainingComments->first(function ($item) use ($thisComment) {
                        return (($item->parent_id === $thisComment->parent_id)
                        && ($item->parent_type === 'App\Models\Comment'));
                    });
                }

                // Is $thisComment a child of a now-nested comment?
                if (!$nextComment) {
                    $remainingCommentsSorted = $remainingComments->sortByDesc('indent_level');
                    foreach($remainingCommentsSorted as $remainingComment) {
                        if (in_array($remainingComment->parent_id, array_column($nestedComments, 'id'))) {
                            $nextComment = $remainingComment;
                            break;
                        }
                    }
                }

                // All above failed, $nextComment must be a reply to the article...
                if (!$nextComment) {
                    $nextComment = $remainingComments->first(function ($item) {
                        return $item->parent_type === 'App\Models\Article';
                    });
                }

                // Catch last comment in array if not caught by above (not response to article: must have parent in $nestedComments)
                // Do I need this???
                if (!$nextComment && count($remainingComments) === 1) {
                    $nextComment = $remainingComments->first();
                }

                if ($nextComment && !$comment) { 
                    $comment = $comments->first(function ($item) use ($nextComment) {
                        return ($item->id === $nextComment->parent_id);
                    });
                }
                
                if ($nextComment && $comment) {
                    $nextComment['reply_to'] = $comment['comment_author'];
                    $nextComment['parent_created_at'] = Carbon::parse($comment['created_at'])->format('j M Y, H:i');
                }

                if ($nextComment && count($nestedComments) < count($comments)) {
                    $nextComment['created'] = Carbon::parse($nextComment['created_at'])->format('j M Y, H:i');

                    $nextComment->approval_user_id === auth()->id() ? $nextComment['user_approves'] = true : $nextComment['user_approves'] = false;
                    
                    if (!in_array($nextComment->id, array_column($nestedComments, 'id'))) {
                        array_push($nestedComments, $nextComment);
                        $nestedCommentsCollection->push($nextComment);
                    }

                    $remainingComments = $remainingComments->filter(function ($item) use ($nextComment) {
                        return $item->id !== $nextComment->id;
                    });
                }
                
                $thisComment = $nextComment;
                $nextComment = null;
                $comment = false;
            }
        }
        
        return $nestedComments;
    }
}