<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AffiliateRequestRequest extends FormRequest
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
            'requested_by' => 'required|uuid',
            'requested_of' => 'required|uuid',
            'network_id' => 'required|uuid'
        ];
    }
}
