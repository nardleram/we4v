<?php

namespace App\Actions\Articles;

use Carbon\Carbon;
use App\Models\Comment;
use App\Actions\Comments\NestComments;

class CompileArticle
{
    public function __construct(NestComments $nestComments)
    {
        $this->nestComments = $nestComments;
    }

    public function compileArticle($rawArticles) : array
    {
        $article = [];
        $tags = [];
        $approvals = [];
        $commentApprovals = [];
        $commentApprovalsCollection = collect();
        $tagCount = 0;
        $commentCount = 0;
        $loop = 0;
        $currentTagId = 0;

        $nestedComments = $this->nestComments->handle(Comment::where('commentable_id', $rawArticles[0]['id'])
            ->join('users AS Us', function ($join) {
                $join->on('Us.id', '=', 'comments.user_id');
            })
            ->join('images AS Im', function ($join) {
                $join->on('Us.id', '=', 'Im.imageable_id')
                ->where('Im.format', 'profile');
            })
            ->select([
                'comments.body as body',
                'comments.id as id',
                'comments.created_at as created_at',
                'comments.parent_id as parent_id',
                'comments.indent_level as indent_level',
                'comments.commentable_id as commentable_id',
                'comments.commentable_type as commentable_type',
                'comments.parent_type as parent_type',
                'Us.id as user_id',
                'Us.username as comment_author',
                'Us.slug as author_slug',
                'Im.path as path'
            ])
            ->orderBy('created_at')
            ->get()
        );

        $userApprovesArticle = $rawArticles->first(function ($item) {
            return ($item->approval_user_id === auth()->id());
        });
        $userApprovesArticle ? $userApprovesArticle = true : $userApprovesArticle = false;

        foreach ($rawArticles as $rawArticle) {
            if ($loop > 0 && $rawArticle->tag_id !== $currentTagId) {
                ++$tagCount;
            }

            // Count article approvals
            if ($rawArticle->approval_id && !in_array($rawArticle->approval_id, $approvals)) {
                array_push($approvals, $rawArticle->approval_id);
            }

            //Gather article data
            $article[0]['id'] = $rawArticle->id;
            $article[0]['body'] = $rawArticle->body;
            $article[0]['created_at'] = Carbon::parse($rawArticle->created_at)->format('D j M Y, H:i');
            $article[0]['name'] = $rawArticle->name;
            $article[0]['num_approvals'] = count($approvals);
            $article[0]['path'] = $rawArticle->path ? $rawArticle->path : 'images/nobody.png';
            $article[0]['slug'] = $rawArticle->slug;
            $article[0]['surname'] = $rawArticle->surname;
            $article[0]['synopsis'] = $rawArticle->synopsis;
            $article[0]['title'] = $rawArticle->title;
            $article[0]['userslug'] = $rawArticle->userslug;
            $article[0]['user_approves'] = $userApprovesArticle;
            $article[0]['comments'] = [];

            // Tags
            if (!in_array($rawArticle->tag, $tags)) {
                array_push($tags, $rawArticle->tag);
                $article[0]['tags'][$tagCount]['tag'] = $rawArticle->tag;
            }

            // Gather comment approvals into collection
            if (!in_array($rawArticle->comment_approval_id, $commentApprovals) && $rawArticle->comment_approval_id) {
                array_push($commentApprovals, $rawArticle->comment_approval_id);
                
                $commentApprovalsCollection->push(['comment_id' => $rawArticle->approved_comment_id, 'user_id' => $rawArticle->comment_approval_user_id]);
            }
            ++$loop;
            $currentTagId = $rawArticle->tag_id;
        }
        
        // Comments
        if (count($nestedComments) > 0) {

            forEach ($nestedComments as $comment) {
                $article[0]['comments'][$commentCount]['body'] = $comment->body;
                $article[0]['comments'][$commentCount]['id'] = $comment->id;
                $article[0]['comments'][$commentCount]['indent_level'] = $comment->indent_level;
                $article[0]['comments'][$commentCount]['comment_author'] = $comment->comment_author;
                $article[0]['comments'][$commentCount]['reply_to'] = $comment->reply_to;
                $article[0]['comments'][$commentCount]['parent_created_at'] = $comment->parent_created_at;
                $article[0]['comments'][$commentCount]['user_approves'] = $commentApprovalsCollection->contains(function ($item) use ($comment) { 
                    return $item['comment_id'] === $comment->id && $item['user_id'] === auth()->id(); 
                });
                $article[0]['comments'][$commentCount]['num_approvals'] = count($commentApprovalsCollection->where('comment_id', $comment->id));
                $article[0]['comments'][$commentCount]['author_slug'] = $comment->author_slug;
                $article[0]['comments'][$commentCount]['comment_path'] = $comment->path ? $comment->path : 'images/nobody.png';
                $article[0]['comments'][$commentCount]['commented_at'] = $comment->created;

                ++$commentCount;
            } 
        } else {
            $article[0]['comments'] = [];
        }
        
        return $article;
    }
}