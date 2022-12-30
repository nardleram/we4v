<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class PidgenMail extends Model
{
    use Uuids;

    public function mail_records()
    {
        $this->hasMany(MailRecord::class);
    }

    public static function getPidgenMailAddressees()
    {
        $assocs = Association::getAssociations();

        $usernames = User::getAddresseeUsernames($assocs);

        $groups = Group::getAddresseeGroups();

        $teams = Team::getAddresseeTeams();

        $adminGroups = Membership::getAddresseeAdminGroups();

        $adminTeams = Membership::getAddresseeAdminTeams();

        $networks = Network::getAddresseeNetworks();

        return array_merge($usernames, $groups, $teams, $adminGroups, $adminTeams, $networks);

    }
}
