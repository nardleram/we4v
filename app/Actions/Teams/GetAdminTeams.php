<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\Membership;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class GetAdminTeams
{
    use SoftDeletes;

    private $compileTeamsArray;

    public function __construct(CompileTeamsArray $compileTeamsArray)
    {
        $this->compileTeamsArray = $compileTeamsArray;
    }
    
    public function handle($userId) : array
    {
        $ids = '';
        $teamIds = Membership::where('is_admin', true)
        ->where('confirmed', true)
        ->where('user_id', $userId)
        ->where('membershipable_type', 'App\\Models\\Team')
        ->get('membershipable_id AS team_id');

        foreach($teamIds as $id) {
            $ids .= "'$id->team_id',";
        }

        $trimmedIds = substr($ids, 0, -1);

        if (count($teamIds)) {
            $rawTeams = DB::select("SELECT teams.name AS team_name,
                teams.id AS team_id,
                teams.function AS team_function,
                Me.role AS member_role,
                Me.confirmed AS member_confirmed,
                Me.is_admin AS member_admin,
                Me.updated_by AS member_updated_by,
                Gr.name AS group_name,
                Gr.id AS group_id,
                Us1.username AS team_owner,
                Us2.username AS member_username,
                Us2.id AS member_user_id,
                Im.path AS path
            FROM teams
            LEFT OUTER JOIN memberships Me
            ON teams.id = Me.membershipable_id
            LEFT OUTER JOIN groups Gr
            ON Gr.id = teams.group_id
            LEFT OUTER JOIN users Us1
            ON Us1.id = teams.owner
            LEFT OUTER JOIN users Us2
            ON Us2.id = Me.user_id
            LEFT OUTER JOIN images Im
            ON Im.imageable_id = Us2.id
            WHERE teams.id IN 
                ($trimmedIds)
            AND Im.format = 'profile'
            AND Me.deleted_at IS null"
            );
        } else {
            $rawTeams = [];
        }
        
        return $this->compileTeamsArray->compileTeams($rawTeams);
    }
}