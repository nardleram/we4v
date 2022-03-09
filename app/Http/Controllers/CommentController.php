<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Actions\Comments\StoreComment;

class CommentController extends Controller
{
    public function __construct(StoreComment $storeComment)
    {
        $this->storeComment = $storeComment;
    }

    public function store(CommentRequest $request)
    {
        $comment = $this->storeComment->handle($request);

        $comment['commented_at'] = $comment->created_at->diffForHumans();

        // Need to check whether redirect is to MyArticles or Talkboard 
        // and respond appropriately

        return redirect()->back();
    }
}
