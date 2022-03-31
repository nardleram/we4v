<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferGroupOwnershipRequest extends FormRequest
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
            'group_id' => 'required|uuid',
            'user_id' => 'required|uuid'
        ];
    }
}
