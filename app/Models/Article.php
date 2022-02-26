<?php

namespace App\Models;

use App\Models\Tag;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['title', 'slug', 'body', 'user_id'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function comments() 
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvalable');
    }

    public function tags()
    {
        return $this->morphMany(Tag::class, 'tagable');
    }
}
