<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class MailRecord extends Model
{
    use Uuids;

    public function pidgen_mails()
    {
        return $this->belongsTo(PidgenMail::class);
    }
}
