<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Article;
use App\Actions\Tags\StoreTags;
use App\Actions\Articles\GetArticle;
use App\Actions\Articles\GetArticles;
use App\Actions\Articles\StoreArticle;
use App\Actions\Articles\SearchArticles;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\SearchArticleRequest;

class ArticleController extends Controller
{
    public function __construct(
        GetArticles $getArticles,
        GetArticle $getArticle,
        StoreArticle $storeArticle,
        StoreTags $storeTags,
        SearchArticles $searchArticles
        )
    {
        $this->getArticles = $getArticles;
        $this->getArticle = $getArticle;
        $this->storeArticle = $storeArticle;
        $this->searchArticles = $searchArticles;
        $this->storeTags = $storeTags;
    }

    public function index()
    {
        return Inertia::render('MyArticles', [
            'myArticles' => $this->getArticles->handle(auth()->id())
        ]);
    }

    public function show(Article $article)
    {
        return Inertia::render('ArticleShow', [
            'article' => $this->getArticle->handle($article->id)
        ]); 
    }

    public function store(StoreArticleRequest $request)
    {
        $article = $this->storeArticle->handle($request);

        $this->storeTags->handle($request, $article->id);

        return redirect()->back()->with([
            'myArticles' => $this->getArticles->handle(auth()->id()),
            'flash' => ['message' => 'Article saved']
        ]);
    }

    public function search(SearchArticleRequest $string)
    {
        session(['searchData' => null]);

        return redirect()->back()->with([
            'searchResults' => [session(['searchData' => $this->searchArticles->handle($string->searchString)])]
        ]);
    }
}
