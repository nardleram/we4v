<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipDeleted extends Mailable
{
    use Queueable, SerializesModels;

    public $membership;
    public $user;

    public function __construct(Membership $membership, User $user)
    {
        $this->membership = $membership;
        $this->user = $user;
    }

    public function build() : object
    {
        $subject = "{$this->user->username} deleted one of your memberships";

        return $this->subject($subject)
            ->view('emails.memberships.deleted');
    }
}
