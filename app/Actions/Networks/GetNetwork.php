<?php

namespace App\Actions\Networks;

use App\Models\Network;

class GetNetwork
{
    public function handle($networkId)
    {
        return Network::where('networks.id', $networkId)
            ->join('memberships', function ($join) {
                $join->on('memberships.membershipable_id', '=', 'networks.id');
            })
            ->leftJoin('groups', function ($join) {
                $join->on('memberships.group_id', '=', 'groups.id');
            })
            ->get();
    }
}
