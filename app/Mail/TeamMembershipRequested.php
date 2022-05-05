<?php

namespace App\Mail;

use App\Models\Membership;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeamMembershipRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;

    public function __construct(Membership $membership)
    {
        $this->membership = $membership;
    }

    public function build()
    {
        $subject = "{$this->membership->membershipable->user->username} sent you a team invitation";

        return $this->subject($subject)
            ->view('emails.teams.requested');
    }
}
