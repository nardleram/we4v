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
            'function' => 'required|string|max:255',
            'assocs' => 'required',
            'membership_type' => 'required|string|max:20',
            'role' => 'required|string|max:50'
        ];
    }
}
