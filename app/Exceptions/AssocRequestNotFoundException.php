<?php

namespace App\Exceptions;

use Exception;

class AssocRequestNotFoundException extends Exception
{
    public function render($request) {
        return response([
            'errors' => [
                'code' => 422,
                'title' => 'Association request not found',
                'detail' => 'The association request you responded to does not exist in our database.'
            ]
        ], 422);
    }
}
