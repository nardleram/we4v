<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequestResponseRequest extends FormRequest
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
            'requester' => 'required|uuid',
            'membershipable_type' => 'required|string|max:20',
            'confirmed' => 'required|boolean'
        ];
    }
}
