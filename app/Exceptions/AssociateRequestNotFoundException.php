<?php

namespace App\Exceptions;

use Exception;

class AssociateRequestNotFoundException extends Exception
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
                'code' => 404,
                'title' => 'Associate request not found/permitted',
                'detail' => 'Submitted associate request not permitted / not found in our database.'
            ]
        ], 404);
    }
}
