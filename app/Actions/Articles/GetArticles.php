<?php

namespace App\Actions\Articles;

use App\Models\Article;
use Illuminate\Database\QueryException;

class GetArticles
{
    public function handle($userId)
    {
        try {
            $rawArticles = Article::where('user_id', $userId)->get();
        } catch (QueryException $exception) {
            return view('articles.queryException');
        } catch (\Exception $exception) {
            return view('articles.queryException');
        }

        // Assemble $articles array here
        
        
        return $rawArticles;
    }
}