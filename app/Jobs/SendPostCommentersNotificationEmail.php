<?php

namespace App\Jobs;

use App\Mail\CommentPostedOnCommentedPost;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPostCommentersNotificationEmail implements ShouldQueue
{
    protected $comment;
    public $tries = 10;
    public $backoff = 3;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        User::whoCommentedOnPost($this->comment->commentable)
            ->get()
            ->filter(function (User $user) {
                return $user->id !== $this->comment->user_id;
            })->map(function (User $user) {
                if ($user->id !== $this->comment->commentable->user_id) { // No need to notify post owner
                    ThrottleMail::dispatch(
                        new CommentPostedOnCommentedPost($this->comment, $user),
                        $user
                    );
                }
            });
    }
}
