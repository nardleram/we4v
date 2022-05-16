<?php

namespace App\Mail;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipAccepted extends Mailable  implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;
    public $type;

    public function __construct(Membership $membership, $type)
    {
        $this->membership = $membership;
        $this->type = $type;
    }

    public function build() : object
    {
        $subject = "{$this->membership->member->username} accepted your {$this->type} invitation";

        return $this->subject($subject)
            ->view('emails.memberships.accepted');
    }
}
