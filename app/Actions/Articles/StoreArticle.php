<?php

namespace App\Actions\Articles;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreArticle
{
    public function handle(Request $request)
    {
        return Article::create([
            'title' => $request->title,
            'synopsis' => $request->synopsis,
            'slug' => Str::slug($request->title).'-'.auth()->id(),
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);
    }
}