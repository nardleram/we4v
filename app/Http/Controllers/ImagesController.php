<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;

class ImagesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'profile' => 'image|mimes:jpg,jpeg,png|max:1024|nullable',
            'bkgrnd' => 'image|mimes:jpg,jpeg,png|max:2048|nullable',
        ]);

        foreach ($request->files as $image) {
            $currentFile = Image::where('imageable_id', auth()->id())
                ->where('format', $request->imageFormat)
                ->first();
                
            isset($currentFile) 
                ? File::delete(public_path('/storage/'.$currentFile->path)) 
                : null;

            $mime = '.'.substr($image[$request->imageFormat]->getMimeType(), 6);

            $fileName = Str::random(15);

            $request->file('selectedImage')[$request->imageFormat]->storeAs('public/images', $fileName.$mime);

            Image::updateOrCreate(
                [
                    'imageable_id' => auth()->id(), 
                    'format' => $request->imageFormat, 
                    'imageable_type' => 'App\Models\\'.$request->model
                ],
                [
                    'path' => 'images/'.$fileName.$mime
                ]
            );
        }

        return Redirect::route('myprofile', auth()->id());
    }
}
