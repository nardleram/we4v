<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes, Uuids;

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
        'slug',
        'email',
        'password',
        'email_verified_at'
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
        'deleted_at',
        'email_verified_at',
        'password',
        'remember_token',
        'two_factor_enabled',
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

    public function approvedPosts() : object
    {
        return $this->belongsToMany(Post::class, 'approvals', 'user_id', 'post_id');
    }

    public function posts() : object
    {
        return $this->hasMany(Post::class);
    }

    public function comments() : object
    {
        return $this->hasMany(Comment::class);
    }

    public function articles() : object
    {
        return $this->hasMany(Article::class);
    }

    public function memberships() : object
    {
        return $this->hasMany(Membership::class);
    }

    public function images() : object
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function associations() : object
    {
        return $this->belongsToMany(User::class, 'associations', 'requested_of', 'requested_by');
    }

    public function notes() : object
    {
        return $this->hasMany(Note::class);
    }

    public function scopeWhoCommentedOnPost(Builder $query, Post $post)
    {
        return $query->whereHas('comments', function ($query) use ($post) {
            return $query->where('commentable_id', $post->id);
        });
    }

    public function scopeWhoCommentedOnArticle(Builder $query, Article $article)
    {
        return $query->whereHas('comments', function ($query) use ($article) {
            return $query->where('commentable_id', $article->id);
        });
    }
}
