<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentPostedOnCommentedArticle extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $comment;
    public $user;

    public function __construct(Comment $comment, User $user)
    {
        $this->comment = $comment;
        $this->user = $user;
    }

    public function build()
    {
        $subject = "{$this->comment->user->username} commented on an article you commented on";

        return $this->subject($subject)
            ->view('emails.articles.comment-on-commented-article');
    }
}
