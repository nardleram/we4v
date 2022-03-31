<?php

namespace App\Actions\Articles;

use App\Models\Article;
use Illuminate\Database\QueryException;

class GetArticles
{
    private $compileArticles;

    public function __construct(CompileArticles $compileArticles)
    {
        $this->compileArticles = $compileArticles;
    }

    public function handle($userId) : array
    {
        $articles = Article::where('articles.user_id', $userId)
            ->leftJoin('tags', function ($join) {
                $join->on('tags.tagable_id', '=', 'articles.id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'articles.user_id');
            })
            ->select([
                'articles.id AS id',
                'articles.title AS title',
                'articles.created_at AS created_at',
                'articles.synopsis AS synopsis',
                'articles.slug AS slug',
                'tags.name AS tag',
                'tags.id AS tag_id'
            ])
            ->get();
        
        return $this->compileArticles->compileArticles($articles);
    }
}