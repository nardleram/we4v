<?php

namespace App\Actions\Networks;

use App\Models\Network;

class TransferNetworkOwnership
{
    public function handle($request) : void
    {
       Network::where('id', $request->network_id)->update(['owner' => $request->user_id]);
    }
}