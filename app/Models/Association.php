<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Association extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'confirmed_at', 'requested_of', 'requested_by'];

    public static function getAssociations() : array
    {
        $ids = [];
        
        $associates = (new static())
            ->select('requested_by', 'requested_of')
            ->where('status', 1)
            ->where(function ($query) {
                return $query->where('requested_of', auth()->id())
                    ->orWhere('requested_by', auth()->id());
            })->get();

        foreach ($associates as $associate) {
            array_push($ids, $associate->requested_by);
            array_push($ids, $associate->requested_of);
        }

        $assocIds = array_unique($ids);
        if (count($assocIds) === 0) {
            array_push($assocIds, auth()->id());
        }

        return $assocIds;
    }

    public static function getPendingAssociations()
    {
        return (new static())
            ->where('status', 0)
            ->where(function ($query) {
                return $query->where('requested_of', auth()->user()->id)
                    ->orWhere('requested_by', auth()->user()->id);
            })
            ->get(['requested_by', 'requested_of', 'created_at', 'id']);
    }
}
