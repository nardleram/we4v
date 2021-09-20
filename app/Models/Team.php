<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Team extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = ['name', 'function', 'owner', 'group_id', 'function'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public static function getTeams()
    {
        return  (new static())
            ->where('owner', auth()->id())
            ->get(['name', 'id', 'owner'])->toArray();
    }
}
