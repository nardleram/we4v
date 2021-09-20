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
            'owner' => $request->owner
        ]);
    }

    public function storeAssocMembers(Request $request, $groupId) : void
    {
        foreach ($request->assocs as $assoc) {
            Membership::create([
                'membershipable_id' => $groupId,
                'membershipable_type' => $request->membership_type,
                'user_id' => $assoc,
                'role' => 'user'
            ]);
        }
    }

    public function storeGroupMember(Request $request, $groupId) : void
    {
        Membership::create([
            'membershipable_id' => $groupId,
            'membershipable_type' => $request->membership_type,
            'group_id' => $request->groupMemberId,
            'role' => 'group'
        ]);
    }
}