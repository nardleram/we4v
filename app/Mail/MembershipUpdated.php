<?php

namespace App\Mail;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;
    public $roleChanged;
    public $adminChanged;

    public function __construct(Membership $membership, $roleChanged, $adminChanged)
    {
        $this->membership = $membership;
        $this->roleChanged = $roleChanged;
        $this->adminChanged = $adminChanged;
    }

    public function build() : object
    {
        $subject = "{$this->membership->membershipable->user->username} changed one of your memberships";

        return $this->subject($subject)
            ->view('emails.memberships.updated');
    }
}
