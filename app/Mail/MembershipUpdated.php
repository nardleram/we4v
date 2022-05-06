<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MembershipUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;
    public $user;
    public $roleChanged;
    public $adminChanged;

    public function __construct(Membership $membership, User $user, $roleChanged, $adminChanged)
    {
        $this->membership = $membership;
        $this->user = $user;
        $this->roleChanged = $roleChanged;
        $this->adminChanged = $adminChanged;
    }

    public function build() : object
    {
        $subject = "{$this->user->username} changed one of your memberships";

        return $this->subject($subject)
            ->view('emails.memberships.updated');
    }
}
