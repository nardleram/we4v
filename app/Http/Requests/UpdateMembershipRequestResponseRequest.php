<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMembershipRequestResponseRequest extends FormRequest
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
            'membershipable_id' => 'required|uuid',
            'confirmed' => 'required|boolean',
            'group_id' => 'nullable|uuid'
        ];
    }
}
