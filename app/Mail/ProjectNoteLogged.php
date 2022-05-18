<?php

namespace App\Mail;

use App\Models\Membership;
use App\Models\Note;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectNoteLogged extends Mailable implements ShouldQueue
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

    public function build() :object
    {
        $subject = "{$this->user->username} logged a note in {$this->note->noteable->name}, a project assigned to {$this->membership->membershipable->name}";

        return $this->subject($subject)
            ->view('emails.projects.note-logged');
    }
}
