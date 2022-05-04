<?php

namespace App\Mail;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipDeleted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;

    public function __construct(Membership $membership)
    {
        $this->membership = $membership;
    }

    public function build() : object
    {
        $subject = "{$this->membership->membershipable->user->username} deleted one of your memberships";

        return $this->subject($subject)
            ->view('emails.memberships.deleted');
    }
}
