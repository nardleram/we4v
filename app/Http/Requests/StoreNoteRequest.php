<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
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
            'body' => 'required|string',
            'noteable_id' => 'required|uuid',
            'noteable_type' => 'required|string|max:20'
        ];
    }
}
