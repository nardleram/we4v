<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Mews\Purifier\Casts\CleanHtml;

class Comment extends Model
{
    use Uuids;

    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    protected $casts = ['body' => CleanHtml::class];

    public function user()
    {
        return $this->belongsTo(User::class);
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
