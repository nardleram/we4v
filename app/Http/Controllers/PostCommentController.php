<?php

namespace App\Http\Controllers;

use App\Jobs\ThrottleMail;
use App\Mail\PostCommented;
use App\Jobs\SendPostCommentersNotificationEmail;
use App\Http\Requests\CommentRequest;
use App\Actions\Comments\StoreComment;

class PostCommentController extends Controller
{
    public function __construct(StoreComment $storeComment)
    {
        $this->storeComment = $storeComment;
    }

    public function store(CommentRequest $request)
    {
        $comment = $this->storeComment->handle($request);

        $comment['commented_at'] = $comment->created_at->diffForHumans();

        if ($comment->commentable->user->id !== auth()->id()) {
            ThrottleMail::dispatch(new PostCommented($comment), $comment->commentable->user);
        }

        SendPostCommentersNotificationEmail::dispatch($comment);

        // Need to check whether redirect is to MyArticles or Talkboard 
        // and respond appropriately

        return redirect()->back();
    }
}
