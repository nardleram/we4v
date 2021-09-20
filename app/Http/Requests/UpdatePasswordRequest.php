<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Actions\Fortify\PasswordValidationRules;

class UpdatePasswordRequest extends FormRequest
{
    use PasswordValidationRules;
    
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
            'password' => 'required|password',
            'newPassword' => $this->passwordRules(),
            'newPassword_confirmation' => 'required|string|max:512',
        ];
    }
}
