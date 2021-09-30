<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuids;
    
    protected $fillable = ['body', 'user_id'];
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    public function user() : object
    {
        return $this->belongsTo(User::class);
    }

    public function approvals() : object
    {
        return $this->morphMany(Approval::class, 'approvalable');
    }

    public function comments() : object
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function commentUsers() : object
    {
        return $this->hasManyThrough(User::class, Comment::class, 'user_id', 'id', 'id', 'commentable_id');
    }

    public function images() : object
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public static function getAllPostData($ids) : object
    {
        return (new static())
            ->select('user_id')
            ->whereIn('posts.user_id', $ids)
            ->leftJoin('approvals', function ($join) {
                $join->on('posts.id', '=', 'approvals.approvalable_id')
                    ->where('approvalable_type', '=', 'App\Models\Post');
            })
            ->leftJoin('comments', function ($join) {
                $join->on('posts.id', '=', 'comments.commentable_id')
                    ->where('comments.commentable_type', '=', 'App\Models\Post');
            })
            ->leftJoin('images', function ($join) {
                $join->on('posts.id', '=', 'images.imageable_id')
                    ->where('images.imageable_type', '=', 'App\Models\Post');
            })
            ->select([
                'posts.id as post_id',
                'posts.user_id as posted_by',
                'posts.body',
                'posts.created_at',
                'posts.updated_at',
                'approvals.id as approval_id',
                'approvals.approvalable_id as approval_post_id',
                'approvals.user_id as approval_user_id',
                'comments.id as comment_id',
                'comments.commentable_id as comment_post_id',
                'comments.body as comment_body',
                'comments.user_id as comment_posted_by',
                'comments.created_at as commented_at',
                'images.path as path'
                ])
            ->groupBy([
                'posts.id',
                'comments.id',
                'approvals.id',
                'approvals.approvalable_id',
                'approvals.user_id',
                'images.path'
            ])
            ->orderBy('posts.created_at', 'DESC')
            ->orderBy('comments.created_at', 'ASC')
            ->get();
    }

    public static function compilePostsArray($rawPosts, $users) : array
    {
        $postCount = 0;
        $commentCount = 0;
        $currentPostId = 0;
        $currentCommentId = 0;
        $approvalId = 0;
        $num_approvals = [];
        $loop = 0;
        $posts = [];

        foreach($rawPosts as $rawPost) {
            // Only increment postCount, reset commentCount and empty approval-count array if new post_id
            if (($currentPostId !== $rawPost->post_id) && $loop > 0) {
                ++$postCount;
                $commentCount = 0;
                $num_approvals = [];
            }
            
            if ($rawPost->post_id === $rawPost->comment_post_id) { //Post has comment(s)

                foreach ($users as $user) {
                    if (($rawPost->comment_posted_by === $user->user_id) && ($currentCommentId !== $rawPost->comment_id)) {
                        $posts[$postCount]['comments'][$commentCount]['commented_by'] = $user->username;
                        $posts[$postCount]['comments'][$commentCount]['user_id'] = $user->user_id;
                        $user->path
                            ? $posts[$postCount]['comments'][$commentCount]['user_profile_pic'] = $user->path
                            : $posts[$postCount]['comments'][$commentCount]['user_profile_pic'] = 'images/nobody.png'; 
                    }
                }

                if ($currentCommentId !== $rawPost->comment_id) {
                    $posts[$postCount]['comments'][$commentCount]['body'] = $rawPost->comment_body;
                    $posts[$postCount]['comments'][$commentCount]['commented_at'] = Carbon::parse($rawPost->commented_at)->diffForHumans();
                    ++$commentCount;
                }

                if ($commentCount === 1) { // Add post data but once
                    foreach ($users as $user) {
                        if ($rawPost->posted_by === $user->user_id) {
                            $posts[$postCount]['posted_by'] = $user->username;
                            $posts[$postCount]['user_id'] = $user->user_id;
                            $user->path
                                ? $posts[$postCount]['user_profile_pic'] = $user->path
                                : $posts[$postCount]['user_profile_pic'] = 'images/nobody.png';
                        }
                    }
                    $posts[$postCount]['body'] = $rawPost->body;
                    $posts[$postCount]['post_id'] = $rawPost->post_id;
                    $rawPost->path ? $posts[$postCount]['image'] = $rawPost->path : null;
                    $posts[$postCount]['created_at'] = $rawPost->created_at->diffForHumans();
                }

                $posts[$postCount]['num_comments'] = $commentCount;

            } else { // Post has no comments
                foreach ($users as $user) {
                    if ($rawPost->posted_by === $user->user_id) {
                        $posts[$postCount]['posted_by'] = $user->username;
                        $user->path
                            ? $posts[$postCount]['user_profile_pic'] = $user->path
                            : $posts[$postCount]['user_profile_pic'] = 'images/nobody.png';
                    }
                }
                $posts[$postCount]['num_comments'] = 0;
                $posts[$postCount]['post_id'] = $rawPost->post_id;
                $posts[$postCount]['user_id'] = $user->user_id;
                $posts[$postCount]['body'] = $rawPost->body;
                $rawPost->path ? $posts[$postCount]['image'] = $rawPost->path : null;
                $posts[$postCount]['created_at'] = $rawPost->created_at->diffForHumans();
            }

            if ($rawPost->post_id === $rawPost->approval_post_id) { // Post has approval(s)

                if ($approvalId !== $rawPost->approval_id) {
                    array_push($num_approvals, $rawPost->approval_id);
                }

                // authUser approves post?
                if ($rawPost->approval_user_id === auth()->id()) {
                    $posts[$postCount]['user_approves_post'] = true;
                }

                $num_approvals = array_unique($num_approvals);
                $posts[$postCount]['num_approvals'] = count($num_approvals);

            } else { // Post has no approvals
                $posts[$postCount]['num_approvals'] = 0;
            }

            //Retain current relevant ids for subsequent logical comparisons on next iteration
            $currentPostId = $rawPost->post_id;
            $approvalId = $rawPost->approval_id;
            $rawPost->comment_id ? $currentCommentId = $rawPost->comment_id : $currentCommentId = 0;
            $currentPostId = $rawPost->post_id;
            ++$loop;
        }

        return $posts;
    }
}