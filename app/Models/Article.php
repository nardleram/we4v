<?php

namespace App\Models;

use App\Models\Tag;
use App\Traits\Uuids;
use DateTimeInterface;
use Mews\Purifier\Casts\CleanHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuids;

    protected $fillable = ['title', 'slug', 'body', 'user_id', 'synopsis'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $casts = [
        'body' => CleanHtml::class
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('D j M Y, h:m');
    }
    
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
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function tags()
    {
        return $this->morphMany(Tag::class, 'tagable');
    }
}
