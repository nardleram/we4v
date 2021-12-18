<?php

namespace App\Actions\Associations;

use App\Models\Association;
use Illuminate\Http\Request;

class UpdateAssociation
{
    public function accept(Request $request) : int
    {
        return Association::where('id', $request->id)
            ->update([
                'confirmed_at' => now(),
                'status' => 1
            ]);
    }

    public function reject(Request $request) : int
    {
        return Association::where('id', $request->id)->delete();
    }
}