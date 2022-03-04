<?php

namespace App\Actions\Articles;

use App\Models\Article;
use Illuminate\Database\QueryException;

class GetArticle
{
    public function handle($articleId)
    {
        try {
            $article = Article::where('id', $articleId)->get();
        } catch (QueryException $exception) {
            return view('articles.queryException');
        } catch (\Exception $exception) {
            return view('articles.queryException');
        }
        
        return $article;
    }
}