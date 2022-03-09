<?php

namespace App\Actions\Articles;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreArticle
{
    public function handle(Request $request)
    {
        $str=rand();

        return Article::create([
            'title' => $request->title,
            'synopsis' => $request->synopsis,
            'slug' => Str::slug($request->title).'-'.md5($str),
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);
    }
}