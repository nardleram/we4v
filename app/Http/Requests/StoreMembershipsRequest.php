<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipsRequest extends FormRequest
{
    public function authorize()
    {
        if (auth()->id()) {
            return true;
        }
        
        return false;
    }

    public function rules()
    {
        return [
            'parent_id' => 'required|uuid',
            'type' => 'required|string|max:20',
            'members' => 'required'
        ];
    }
}
