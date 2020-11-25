<?php

namespace App\Http\Controllers;

use App\Associate;
use App\Exceptions\AssociateRequestNotFoundException;
use App\Http\Resources\Associate as AssociateResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssociateRequestResponseController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'user_id' => 'required',
            'status' => 'required',
        ]);

        try {
            $associateRequest = Associate::where('user_id', $data['user_id'])
            ->where('associate_id', auth()->user()->id)
            ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new AssociateRequestNotFoundException();
        }
        
        $associateRequest->update(array_merge($data, [
            'confirmed_at' => now(),
        ]));

        return new AssociateResource($associateRequest);
    }

    public function destroy()
    {
        $data = request()->validate([
            'user_id' => 'required',
        ]);

        try {
            Associate::where('user_id', $data['user_id'])
            ->where('associate_id', auth()->user()->id)
            ->firstOrFail()
            ->delete();
        } catch (ModelNotFoundException $e) {
            throw new AssociateRequestNotFoundException();
        }
        
        return response()->json([], 204);
    }
}
