<?php

namespace App\Actions\Memberships;

use App\Models\Membership;

class DeleteMembership
{
    public function handle($id) : void
    {
        Membership::where('id', $id)
            ->forceDelete();
    }
}