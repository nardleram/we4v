<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['name', 'owner', 'description', 'geog_area'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function memberships() : object
    {
        return $this->hasMany(Membership::class);
    }

    public static function getMyNetworkReqs() : object
    {
        return Group::where('groups.owner', auth()->id())
            ->join('memberships', function ($join) {
                $join->on('memberships.group_id', '=', 'groups.id')
                ->where('confirmed', false);
            })
            ->leftJoin('networks', function ($join) {
                $join->on('memberships.membershipable_id', '=', 'networks.id')
                ->where('networks.owner', '!=', auth()->id());
            })
            ->join('users', function ($join) {
                $join->on('networks.owner', '=', 'users.id');
            })
            ->join('images', function ($join) {
                $join->on('users.id', '=', 'imageable_id')
                ->where('format', 'profile');
            })
            ->select([
                'networks.id AS networkId', 
                'networks.name AS networkName',
                'networks.description AS networkDescription',
                'groups.id AS groupId', 
                'groups.name AS groupName', 
                'groups.description AS groupDescription', 
                'groups.geog_area AS groupArea',
                'users.username AS username',
                'users.slug AS userSlug',
                'images.path AS path'
            ])
            ->get();
    }
}
