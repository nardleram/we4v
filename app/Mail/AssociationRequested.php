<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssociationRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $requester;
    public $requestee;

   public function __construct(User $requestee, User $requester)
    {
        $this->requester = $requester;
        $this->requestee = $requestee;
    }

    public function build() : object
    {
        $subject = "{$this->requester->username} requests association";

        return $this->subject($subject)
            ->view('emails.associations.requested');
    }
}
