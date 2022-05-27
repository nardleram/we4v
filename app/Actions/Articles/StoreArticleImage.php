<?php

namespace App\Actions\Articles;

use App\Models\Image;
use Illuminate\Http\Request;

class StoreArticleImage
{
    public function handle(Request $request)
    {
        dd($request[0]);
        try {
            $imagePath = $request['image']->store('images/articles', 'public');
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

        // Temporary uuid: will be replaced by actual article ID
        // when article is saved
        return Image::create([
            'imageable_id' => auth()->id(), 
            'format' => 'article', 
            'imageable_type' => 'App\Models\Article',
            'path' => $imagePath
        ]);
    }
}