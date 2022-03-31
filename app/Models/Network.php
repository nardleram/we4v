<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Network extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public static function getMyNetworks() : array
    {
        $rawNetworks = Network::where('networks.owner', auth()->id())
            ->leftJoin('memberships', function ($join) {
                $join->on('memberships.membershipable_id', '=', 'networks.id');
            })
            ->leftJoin('groups', function ($join) {
                $join->on('memberships.group_id', '=', 'groups.id');
            })
            ->leftJoin('users', function ($join) {
                $join->on('groups.owner', '=', 'users.id');
            })
            ->select([
                'networks.id AS network_id', 
                'networks.name AS network_name', 
                'networks.description AS network_description', 
                'memberships.confirmed AS confirmed',
                'memberships.group_id AS membership_group_id',
                'memberships.created_at AS membership_created_at',
                'groups.id AS group_id', 
                'groups.name AS group_name', 
                'groups.description AS group_description', 
                'groups.geog_area AS geog_area',
                'users.username AS group_owner',
                'users.slug AS user_slug'
            ])
            ->get();

        // Assemble the Avengers
        $networks = [];
        $loop = 0;

        foreach($rawNetworks as $network) {
            if ($loop === 0) {
                $networks[0]['network_id'] = $network->network_id;
                $networks[0]['network_name'] = $network->network_name;
                $networks[0]['network_description'] = $network->network_description;
            }
            
            if ($network->membership_group_id && $network->group_id === $network->membership_group_id) {
                $networks[0]['groups'][$loop]['membership_confirmed'] = $network->confirmed;
                $networks[0]['groups'][$loop]['group_id'] = $network->group_id;
                $networks[0]['groups'][$loop]['group_description'] = $network->group_description;
                $networks[0]['groups'][$loop]['membership_created_at'] = $network->membership_created_at;
                $networks[0]['groups'][$loop]['group_name'] = $network->group_name;
                $networks[0]['groups'][$loop]['group_geog_area'] = $network->geog_area;
                $networks[0]['groups'][$loop]['group_owner'] = $network->group_owner;
                $networks[0]['groups'][$loop]['user_slug'] = $network->user_slug;
            }

            ++$loop;
        }
        
        return $networks;
    }
}
