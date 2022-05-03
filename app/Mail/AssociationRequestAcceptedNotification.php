<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssociationRequestAcceptedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $requester;
    public $requestee;

   public function __construct(User $requestee, User $requester)
    {
        $this->requester = $requester;
        $this->requestee = $requestee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "{$this->requestee->username} accepted your association request";

        return $this->subject($subject)
            ->view('emails.associations.accepted');
    }
}
