<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use App\Jobs\ThrottleMail;
use Illuminate\Http\Request;
use App\Mail\ArticleCommented;
use App\Mail\ArticleCommentRepliedTo;
use App\Jobs\SendArticleCommentersNotificationEmail;

class StoreComment
{
    public function handle(Request $request) : object
    {
        $comment = Comment::create([
            'body' => $request->body,
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $request->commentable_type,
            'parent_id' => $request->parent_id ? $request->parent_id : null,
            'parent_type' => $request->parent_type,
            'indent_level' => $request->indent_level ? $request->indent_level : 0,
            'user_id' => auth()->id(),
        ]);

        $comment->parent_type === 'App\\Models\\Comment'
        ? $replyTo = Comment::where('id', $comment->parent_id)->first()
        : $replyTo = null;

        $comment['commented_at'] = $comment->created_at->diffForHumans();

        if ($comment->commentable->user->id !== auth()->id()) {
            if ($comment->parent_type === 'App\\Models\\Comment') {
                $replyTo->user_id !== auth()->id()
                ? ThrottleMail::dispatch(new ArticleCommented($comment), $comment->commentable->user)
                : null;
            } else {
                ThrottleMail::dispatch(new ArticleCommented($comment), $comment->commentable->user);
            }
        }

        if ($comment->parent_type === 'App\\Models\\Comment') {
            $replyTo->user_id !== auth()->id()
            ? ThrottleMail::dispatch(new ArticleCommentRepliedTo($comment, $replyTo->user), $replyTo->user)
            : SendArticleCommentersNotificationEmail::dispatch($comment);
        }

        return $comment;
    }
}