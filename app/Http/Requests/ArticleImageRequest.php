<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleImageRequest extends FormRequest
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
            'image' => 'image|max:2048|required'
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Images may be no larger than 2MB and must be either jpg, jpeg, png, bmp, gif, svg, or webp'
        ];
    }
}
