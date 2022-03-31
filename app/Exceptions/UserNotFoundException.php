<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function render($request) {
        return response([
            'errors' => [
                'code' => 422,
                'title' => 'User not found',
                'detail' => 'The user you are looking for does not exist in our database.'
            ]
        ], 422);
    }
}
