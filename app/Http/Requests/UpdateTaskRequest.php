<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'end_date' => 'required|date',
            'user_id' => 'nullable|uuid',
            'taskable_id' => 'required|uuid',
            'taskable_type' => 'required|string|max:20',
            'note' => 'array|nullable'
        ];
    }
}
