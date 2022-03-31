<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'owner' => 'required|uuid',
            'group_id' => 'required|uuid',
            'function' => 'required|string|max:255',
            'membershipable_type' => 'required|string|max:20',
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
