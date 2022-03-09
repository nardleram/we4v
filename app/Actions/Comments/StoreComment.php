<?php

namespace App\Actions\Comments;

use App\Models\Comment;
use Illuminate\Http\Request;

class StoreComment
{
    public function handle(Request $request) : object
    {
        return Comment::create([
            'body' => $request->body,
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $request->commentable_type,
            'parent_id' => $request->parent_id ? $request->parent_id : null,
            'parent_type' => $request->parent_type,
            'indent_level' => $request->indent_level ? $request->indent_level : 0,
            'user_id' => auth()->id(),
        ]);
    }
}