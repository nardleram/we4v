<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Associate extends Model
{
    protected $guarded = [];

    protected $dates = ['confirmed_at'];

    public static function association($userId)
    {
        return (new static())
            ->where(function ($query) use ($userId) {
                return $query->where('user_id', auth()->user()->id)
                    ->where('associate_id', $userId);
            })->orWhere(function ($query) use ($userId) {
                return $query->where('associate_id', auth()->user()->id)
                    ->where('user_id', $userId);
            })
            ->first();
    }

    public static function associations()
    {
        return (new static())
            ->whereNotNull('confirmed_at')
            ->where(function ($query) {
                return $query->where('user_id', auth()->user()->id)
                    ->orWhere('associate_id', auth()->user()->id);
            })
            ->get();
    }
}
