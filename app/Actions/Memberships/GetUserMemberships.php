<?php

namespace App\Actions\Memberships;

use Illuminate\Support\Facades\DB;

class GetUserMemberships
{
    public function getUserMemberships($userId)
    {
        $memberships = DB::select("SELECT Me.membershipable_type as membership_type,
            Gr.id as group_id,
            Gr.name as group_name,
            Te.name as team_name,
            Te.id as team_id,
            Us1.username as group_owner,
            Us2.username as team_owner
        FROM memberships Me
        LEFT OUTER JOIN groups Gr
        ON Gr.id = Me.membershipable_id
        LEFT OUTER JOIN teams Te
        ON Te.id = Me.membershipable_id
        LEFT OUTER JOIN users Us1
        ON Gr.owner = Us1.id
        LEFT OUTER JOIN users Us2
        ON Te.owner = Us2.id
        WHERE Me.user_id = '$userId'
        ");

        dd($memberships);
    }
}