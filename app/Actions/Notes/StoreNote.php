<?php

namespace App\Actions\Notes;

use App\Models\Note;

class StoreNote
{
    public function handle($note) : void
    {
        Note::create([
            'body' => $note['body'],
            'noteable_id' => $note['noteable_id'],
            'noteable_type' => $note['noteable_type'],
            'user_id' => auth()->id()
        ]);
    }
}