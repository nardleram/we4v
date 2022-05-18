<?php

namespace App\Mail;

use App\Models\Membership;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamTaskAssigned extends Mailable implements ShouldQueue
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
        $subject = "{$this->user->username} assigned a task to a team you are in: {$this->membership->tasks->first()->taskable->name}";

        return $this->subject($subject)
            ->view('emails.tasks.team-assigned');
    }
}
