<?php

namespace App\Http\Controllers;

use App\Actions\Articles\StoreArticleImage;
use App\Http\Requests\ArticleImageRequest;

class ArticleImageController extends Controller
{
    public function __construct(StoreArticleImage $storeArticleImage)
    {
        $this->storeArticleImage = $storeArticleImage;
    }

    public function store(ArticleImageRequest $request)
    {
        dd($request);
        $image = $this->storeArticleImage->handle($request);

        return redirect()->back()->with([
            'articleImageData' => [session(['articleImagePath' => env("APP_URL", "https://www.we4v.com").'/storage/'.$image->path])]
        ]);
    }
}
