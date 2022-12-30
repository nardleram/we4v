<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Actions\Comments\StoreComment;

class ArticleCommentController extends Controller
{
    public function __construct(private StoreComment $storeComment)
    {}

    public function store(CommentRequest $request)
    {
        $this->storeComment->handle($request);

        return redirect()->back();
    }
}
