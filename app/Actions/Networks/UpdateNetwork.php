<?php

namespace App\Actions\Networks;

use App\Models\Network;

class UpdateNetwork
{
    public function handle($request) : void
    {
        Network::where('id', $request->id)
            ->where('owner', auth()->id())
            ->update([
                'description' => $request->description,
                'name' => $request->name
            ]);
    }
}