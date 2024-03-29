<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'projectable_id' => 'uuid',
            'projectable_type' => 'required|string|max:25',
        ];
    }
}
