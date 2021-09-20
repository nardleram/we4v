<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use App\Exceptions\AssocRequestNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Redirect;

class AssociateRequestResponseController extends Controller
{
    public function update()
    {
        $data = request()->validate([
            'status' => 'required',
            'id' => 'required',
        ]);

        try {
            $associateRequest = Association::where('id', $data['id'])
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new AssocRequestNotFoundException();
        }
        
        if ($data['status'] === 1) {
            $associateRequest->update(array_merge($data, [
                'confirmed_at' => now(),
            ]));
        } else {
            $associateRequest->delete();
        }

        return Redirect::back();

    }
}
