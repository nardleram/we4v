<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoteRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'owner' => 'required|uuid',
            'closing_date' => 'required|date',
            'voteable_type' => 'required|string|max:20',
            'voteable_id' => 'required|uuid',
            'vote_elements' => 'required|array',
        ];
    }
}
