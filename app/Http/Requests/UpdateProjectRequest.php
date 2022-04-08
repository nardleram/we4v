<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'id' => 'required|uuid',
            'owner' => 'required|uuid',
            'name' => 'required|string:50',
            'description' => 'required|string:255',
            'completed' => 'required|boolean',
            'end_date' => 'nullable|date',
            'group_id' => 'nullable|uuid',
            'team_id' => 'nullable|uuid',
            'note' => 'nullable'
        ];
    }
}
