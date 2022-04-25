<?php

namespace App\Mail;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommentPostedOnCommentedPost extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $comment;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Comment $comment, User $user)
    {
        $this->comment = $comment;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "{$this->comment->user->username} commented on a Talkboard post you commented on";

        return $this->subject($subject)
            ->view('emails.posts.comment-on-commented-post');
    }
}
