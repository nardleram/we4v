<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssociateRequestRequest extends FormRequest
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
            'requested_by' => 'required|uuid',
            'requested_of' => 'required|uuid',
        ];
    }
}
