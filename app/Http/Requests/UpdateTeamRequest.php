<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'group_id' => 'required|uuid',
            'function' => 'required|string|max:255',
            'membershipable_type' => 'required|string|max:20',
            'membershipable_id' => 'nullable|uuid',
            'members' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter the team’s name',
            'members.required' => 'Please select at least one member for your team',
            'function.required' => 'Please describe the team’s function',
        ];
    }
}
