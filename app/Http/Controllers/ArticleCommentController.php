<?php

namespace App\Http\Controllers;

use App\Jobs\ThrottleMail;
use App\Mail\ArticleCommented;
use App\Http\Requests\CommentRequest;
use App\Actions\Comments\StoreComment;
use App\Jobs\SendArticleCommentersNotificationEmail;
use App\Mail\ArticleCommentRepliedTo;
use App\Models\Comment;

class ArticleCommentController extends Controller
{
    public function __construct(StoreComment $storeComment)
    {
        $this->storeComment = $storeComment;
    }

    public function store(CommentRequest $request)
    {
        $this->storeComment->handle($request);

        return redirect()->back();
    }
}
