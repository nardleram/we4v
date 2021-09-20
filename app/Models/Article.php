<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Article extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = ['title', 'body', 'user_id'];
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
}
