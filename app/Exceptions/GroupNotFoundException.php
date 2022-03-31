<?php

namespace App\Exceptions;

use Exception;

class GroupNotFoundException extends Exception
{
    public function render($request) {
        return response([
            'errors' => [
                'code' => 422,
                'title' => 'Group not found',
                'detail' => 'The group you requested does not exist in our database.'
            ]
        ], 422);
    }
}
