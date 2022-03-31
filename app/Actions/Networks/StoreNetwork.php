<?php

namespace App\Actions\Networks;

use App\Models\Network;

class StoreNetwork
{
    public function handle($request) : void
    {
        Network::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner' => auth()->id()
        ]);
    }
}