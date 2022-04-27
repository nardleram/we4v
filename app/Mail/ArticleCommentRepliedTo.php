<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArticleCommentRepliedTo extends Mailable
{
    use Queueable, SerializesModels;

    public $comment;
    public $replyToUser;

    public function __construct(Comment $comment, User $replyToUser)
    {
        $this->comment = $comment;
        $this->replyToUser = $replyToUser;
    }

    public function build()
    {
        $subject = "{$this->comment->user->username} replied to your comment under article: {$this->comment->commentable->title}";

        return $this->subject($subject)
            ->view('emails.articles.reply-commented');
    }
}
