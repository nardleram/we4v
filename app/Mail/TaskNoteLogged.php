<?php

namespace App\Mail;

use App\Models\Note;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskNoteLogged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;
    public $user;
    public $note;
    public $type;

    public function __construct(Membership $membership, User $user, Note $note, $type)
    {
        $this->membership = $membership;
        $this->user = $user;
        $this->note = $note;
        $this->type = $type;
    }

    public function build()
    {
        $subject = "{$this->user->username} logged a note in {$this->note->noteable->name}, a task assigned to {$this->note->noteable->taskable->name}";

        return $this->subject($subject)
            ->view('emails.tasks.note-logged');
    }
}
