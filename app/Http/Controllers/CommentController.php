<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\Redirect;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        $comment = Comment::create([
            'body' => $request->body,
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $request->commentable_type,
            'user_id' => $request->user_id,
        ]);

        $comment['commented_at'] = $comment->created_at->diffForHumans();

        return Redirect::route('talkboard');
    }
}
