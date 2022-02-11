<?php

namespace App\Actions\Associations;

use App\Models\Association;
use Illuminate\Http\Request;

class UpdateAssociation
{
    public function accept(Request $request) : void
    {
        Association::where('id', $request->id)
            ->update([
                'confirmed_at' => now(),
                'status' => 1
            ]);
    }

    public function reject(Request $request) : void
    {
        Association::where('id', $request->id)->delete();
    }
}