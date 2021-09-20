<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequestResponseRequest extends FormRequest
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
            'membershipable_id' => 'required|uuid',
            'requester' => 'required|uuid',
            'membershipable_type' => 'required|string|max:20',
            'confirmed' => 'required|boolean'
        ];
    }
}
