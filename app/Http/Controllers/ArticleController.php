<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\Articles\GetArticles;

class ArticleController extends Controller
{
    public function __construct(GetArticles $getArticles)
    {
        $this->getArticles = $getArticles;
    }
    public function index()
    {
        return Inertia::render('MyArticles', [
            'myarticles' => $this->getArticles->handle(auth()->id())
        ]);
    }
}
