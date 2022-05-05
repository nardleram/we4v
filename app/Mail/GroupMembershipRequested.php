<?php

namespace App\Mail;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GroupMembershipRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;

    public function __construct(Membership $membership)
    {
        $this->membership = $membership;
    }

    public function build()
    {
        $subject = "{$this->membership->membershipable->user->username} sent you a group invitation";

        return $this->subject($subject)
            ->view('emails.groups.requested');
    }
}
