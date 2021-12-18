<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Articles\GetArticles;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

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
