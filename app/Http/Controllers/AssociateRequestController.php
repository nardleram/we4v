<?php

namespace App\Http\Controllers;

use App\Associate;
use App\Exceptions\UserNotFoundException;
use App\Http\Resources\Associate as AssociateResource;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssociateRequestController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'associate_id' => 'required',
        ]);

        try {
            User::findOrFail($data['associate_id'])
            ->associates()->syncWithoutDetaching(auth()->user(), ['created_at' => now()]);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException();
        }

        return new AssociateResource(
            Associate::where('user_id', auth()->user()->id)
                ->where('associate_id', $data['associate_id'])
                ->first()
        );
    }
}
