<?php

namespace App\Actions\Articles;

use App\Models\Article;
use Illuminate\Database\QueryException;

class GetArticles
{
    public function handle($userId)
    {

        return Article::where('articles.user_id', $userId)
        ->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'articles.user_id');
        })
        ->select([
            'articles.title as title',
            'articles.body as body',
            'articles.created_at as created_at',
            'articles.synopsis as synopsis',
            'articles.slug as slug',
            'users.name as name',
            'users.surname as surname'
        ])
        ->get();
    }
}