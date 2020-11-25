<?php

namespace App\Exceptions;

use Exception;
use PhpParser\JsonDecoder;

class ValidationErrorException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'errors' => [
                'code' => 422,
                'title' => 'Insufficient/incorrect data submitted',
                'detail' => 'Data supplied is malformed or incomplete.',
                'meta' => json_decode($this->getMessage()),
            ]
        ], 422);
    }
}
