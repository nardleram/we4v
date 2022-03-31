<?php

namespace App\Actions\Articles;

use App\Models\Tag;

class SearchArticles
{
    private $compileArticleResults;

    public function __construct(CompileArticleResults $compileArticleResults)
    {
        $this->compileArticleResults = $compileArticleResults;
    }

    public function handle($string) : array
    {
        $str = trim($string);

        $results = Tag::where('tags.name', '=', $str)
        ->join('articles', function ($join) {
            $join->on('articles.id', '=', 'tags.tagable_id');
        })
        ->join('tags AS T2', function ($join) {
            $join->on('T2.tagable_id', '=', 'articles.id');
        })
        ->select([
            'articles.slug as slug',
            'articles.title as title',
            'articles.synopsis as synopsis',
            'T2.name as tag'
        ])
        ->get();

        return $this->compileArticleResults->handle($results);
    }
}