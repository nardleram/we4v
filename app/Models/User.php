<?php

namespace App\Models;

use App\Traits\Uuids;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Uuids;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'created_at',
        'updated_at',
        'email_verified_at',
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = [
    //     'profile_photo_url',
    // ];

    public static function getUsersData($ids) : object
    {
        return (new static())
            ->select('id as user_id')
            ->whereIn('users.id', $ids)
            ->leftJoin('images', function ($join) {
                $join->on('users.id', '=', 'images.imageable_id')
                    ->where('images.format', '=', 'profile')
                    ->where('images.imageable_type', '=', 'App\Models\User');
            }) 
            ->select('users.id as user_id', 'name', 'surname', 'username', 'images.path as path')
            ->get();
    }

    public function approvedPosts() : object
    {
        return $this->belongsToMany(Post::class, 'approvals', 'user_id', 'post_id');
    }

    public function posts() : object
    {
        return $this->hasMany(Post::class);
    }

    public function articles() : object
    {
        return $this->hasMany(Article::class);
    }

    public function comments() : object
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function images() : object
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function memberships() : object
    {
        return $this->hasMany(Membership::class);
    }

    public function associations() : object
    {
        return $this->belongsToMany(User::class, 'associations', 'requested_of', 'requested_by');
    }
}
