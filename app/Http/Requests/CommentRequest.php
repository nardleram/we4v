<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->id()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => 'required|string',
            'commentable_id' => 'required|uuid',
            'commentable_type' => 'required|string|max:30',
            'parent_id' => 'nullable|uuid',
            'parent_type' => 'nullable|string|max:30',
            'indent_level' => 'nullable|digits:1'
        ];
    }
}
