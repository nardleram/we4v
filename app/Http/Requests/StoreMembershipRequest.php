<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequest extends FormRequest
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
            'membershipable_type' => 'required|string|max:50',
            'group_id' => 'nullable|uuid',
            'user_id' => 'nullable|uuid',
            'admin' => 'nullable|boolean',
            'role' => 'nullable|string|max:50'
        ];
    }
}
