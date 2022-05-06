<?php

namespace App\Mail;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GroupMembershipRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;
    public $user;

    public function __construct(Membership $membership, User $user)
    {
        $this->membership = $membership;
        $this->user = $user;
    }

    public function build()
    {
        $subject = "{$this->user->username} sent you a group invitation";

        return $this->subject($subject)
            ->view('emails.groups.requested');
    }
}
