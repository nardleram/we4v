<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UsersTaskAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $membership;
    public $user;
    public $task;

    public function __construct(Membership $membership, User $user, Task $task)
    {
        $this->membership = $membership;
        $this->user = $user;
        $this->task = $task;
    }

    public function build()
    {
        $subject = "{$this->user->username} assigned a task to you";

        return $this->subject($subject)
            ->view('emails.tasks.user-assigned');
    }
}
