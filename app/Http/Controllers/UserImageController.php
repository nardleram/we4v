<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserImage as UserImageResource;
use Intervention\Image\Facades\Image as Image;

class UserImageController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'image' => '',
            'width' => '',
            'height' => '',
            'pagePosition' => ''
        ]);

        $image = $data['image']->store('user-images', 'public');

        Image::make($data['image'])
            ->fit($data['width'], $data['height'])
            ->save(storage_path('app/public/user-images/'.$data['image']->hashName()));

        $userImage = auth()->user()->images()->create([
            'path' => $image,
            'width' => $data['width'],
            'height' => $data['height'],
            'pagePosition' => $data['pagePosition'],
        ]);

        return new UserImageResource($userImage);
    }
}
