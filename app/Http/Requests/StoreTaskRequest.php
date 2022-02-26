<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'project_id' => 'required|uuid',
            'members' => 'nullable|array',
            'taskable_id' => 'required|uuid',
            'taskable_type' => 'required|string|max:20',
        ];
    }
}
