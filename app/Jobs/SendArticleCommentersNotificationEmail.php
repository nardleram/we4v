<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\CommentPostedOnCommentedArticle;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendArticleCommentersNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $comment;
    public $tries = 10;
    public $backoff = 3;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void
    {
        User::whoCommentedOnArticle($this->comment->commentable)
            ->get()
            ->filter(function (User $user) {
                return $user->id !== $this->comment->user_id;
            })->map(function (User $user) {
                if ($user->id !== $this->comment->commentable->user_id) { // No need to notify article owner
                    ThrottleMail::dispatch(
                        new CommentPostedOnCommentedArticle($this->comment, $user),
                        $user
                    );
                }
            });
    }
}
