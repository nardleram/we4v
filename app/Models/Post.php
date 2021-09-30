<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuids;
    
    protected $fillable = ['body', 'user_id'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function user() : object
    {
        return $this->belongsTo(User::class);
    }

    public function approvals() : object
    {
        return $this->morphMany(Approval::class, 'approvalable');
    }

    public function comments() : object
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function commentUsers() : object
    {
        return $this->hasManyThrough(User::class, Comment::class, 'user_id', 'id', 'id', 'commentable_id');
    }

    public function images() : object
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}