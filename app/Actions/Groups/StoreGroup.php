<?php

namespace App\Actions\Groups;

use App\Models\Group;
use App\Models\Membership;
use Illuminate\Http\Request;

class StoreGroup
{
    public function storeGroup(Request $request) : object
    {
        return Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner' => $request->owner,
            'geog_area' => $request->geog_area
        ]);
    }

    public function storeGroupMember(Request $request, $groupId) : void
    {
        Membership::create([
            'membershipable_id' => $groupId,
            'membershipable_type' => $request->membershipable_type,
            'group_id' => $request->groupMemberId,
            'role' => 'group'
        ]);
    }
}