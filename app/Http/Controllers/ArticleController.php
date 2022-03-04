<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Article;
use App\Actions\Articles\GetArticle;
use App\Actions\Articles\GetArticles;
use App\Actions\Articles\StoreArticle;
use App\Actions\Tags\StoreTags;
use App\Http\Requests\StoreArticleRequest;

class ArticleController extends Controller
{
    public function __construct(GetArticles $getArticles, GetArticle $getArticle, StoreArticle $storeArticle, StoreTags $storeTags)
    {
        $this->getArticles = $getArticles;
        $this->getArticle = $getArticle;
        $this->storeArticle = $storeArticle;
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
}
