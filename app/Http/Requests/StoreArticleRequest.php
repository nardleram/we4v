<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize()
    {
        if (auth()->id()) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'synopsis' => 'required|string',
            'body' => 'required|string',
            'tags' => 'nullable|array'
        ];
    }
}
