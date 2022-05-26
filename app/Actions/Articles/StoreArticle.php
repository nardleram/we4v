<?php

namespace App\Actions\Articles;

use App\Models\Article;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreArticle
{
    public function handle(Request $request)
    {
        $str=rand();

        $article = Article::create([
            'title' => $request->title,
            'synopsis' => $request->synopsis,
            'slug' => Str::slug($request->title).'-'.md5($str),
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        // Give article's images the article's id
        Image::where('imageable_id', auth()->id())
            ->where('imageable_type', 'App\\Models\\Article')
            ->update([
                'imageable_id' => $article->id
            ]);

        return $article;
    }
}