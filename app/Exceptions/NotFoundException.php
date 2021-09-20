<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function render($request)
    {
        return response([
            'code' => 422,
            'title' => 'Object not found',
            'message' => 'The object you requested was not found in our database.'
        ], 422);
    }
}
