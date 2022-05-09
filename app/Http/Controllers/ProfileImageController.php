<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use App\Models\Image;

class ProfileImageController extends Controller
{
    public function store(ProfileImageRequest $request)
    {
        foreach ($request->files as $image) {
            $currentFile = Image::where('imageable_id', auth()->id())
                ->where('format', 'profile')
                ->first();
                
            isset($currentFile) 
                ? File::delete(public_path('/storage/'.$currentFile->path)) 
                : null;

            $mime = '.'.substr($image->getMimeType(), 6);

            $fileName = Str::random(15);

            $request->file('image')->storeAs('public/images', $fileName.$mime);

            Image::updateOrCreate(
                [
                    'imageable_id' => auth()->id(), 
                    'format' => 'profile', 
                    'imageable_type' => 'App\Models\User'
                ],
                [
                    'path' => 'images/'.$fileName.$mime
                ]
            );
        }

        return Redirect::route('myprofile', auth()->id());
    }
}
