<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
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
            'owner' => 'required|uuid',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'geog_area' => 'nullable|string|max:255',
            'members' => 'nullable|array',
            'membershipable_type' => 'required|string|max:20'
        ];
    }
}
