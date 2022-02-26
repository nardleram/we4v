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
            'id' => 'nullable|uuid',
            'end_date' => 'required|date',
            'taskable_id' => 'required|uuid',
            'taskable_type' => 'required|string|max:20',
            'membershipable_id' => 'required|uuid',
            'membershipable_type' => 'required|string|max:20',
            'completed' => 'required|boolean',
            'taskNote' => 'array|nullable',
            'projectNote' => 'array|nullable',
            'members' => 'array|nullable'
        ];
    }
}
