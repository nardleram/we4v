<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileImageRequest extends FormRequest
{
    public function authorize() : bool
    {
        if (auth()->id()) {
            return true;
        }

        return false;
    }

    public function rules() : array
    {
        return [
            'image' => 'image|max:1024|required'
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Images may be no larger than 1MB and must be one of jpg, jpeg, png, bmp, gif, svg, or webp',
        ];
    }
}
