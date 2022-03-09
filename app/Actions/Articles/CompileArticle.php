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
        $tagCount = 0;
        $commentCount = 0;
        $loop = 0;
        $currentTagId = 0;

        $nestedComments = $this->nestComments->handle(Comment::where('commentable_id', $rawArticles[0]['id'])
            ->join('users AS Us1', function ($join) {
                $join->on('Us1.id', '=', 'comments.user_id');
            })
            ->join('images AS Im1', function ($join) {
                $join->on('Us1.id', '=', 'Im1.imageable_id')
                ->where('Im1.format', 'profile');
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
                'Us1.username as comment_author',
                'Us1.slug as author_slug',
                'Im1.path as path'
            ])
            ->orderBy('created_at')
            ->get()
        );

        foreach ($rawArticles as $rawArticle) {
            if ($loop > 0 && $rawArticle->tag_id !== $currentTagId) {
                ++$tagCount;
            }

            $article[0]['id'] = $rawArticle->id;
            $article[0]['body'] = $rawArticle->body;
            $article[0]['created_at'] = Carbon::parse($rawArticle->created_at)->format('D j M Y, H:i');
            $article[0]['name'] = $rawArticle->name;
            $article[0]['num_approvals'] = 3;
            $article[0]['path'] = $rawArticle->path;
            $article[0]['slug'] = $rawArticle->slug;
            $article[0]['surname'] = $rawArticle->surname;
            $article[0]['synopsis'] = $rawArticle->synopsis;
            $article[0]['title'] = $rawArticle->title;
            $article[0]['userslug'] = $rawArticle->userslug;
            $article[0]['user_approves'] = false;
            $article[0]['comments'] = [];

            // Tags
            if (!in_array($rawArticle->tag, $tags)) {
                array_push($tags, $rawArticle->tag);
                $article[0]['tags'][$tagCount]['tag'] = $rawArticle->tag;
            }

            ++$loop;
            $currentTagId = $rawArticle->tag_id;
        }

        // Comments
        forEach ($nestedComments as $comment) {
            $article[0]['comments'][$commentCount]['body'] = $comment->body;
            $article[0]['comments'][$commentCount]['id'] = $comment->id;
            $article[0]['comments'][$commentCount]['indent_level'] = $comment->indent_level;
            $article[0]['comments'][$commentCount]['comment_author'] = $comment->comment_author;
            $article[0]['comments'][$commentCount]['reply_to'] = $comment->reply_to;
            $article[0]['comments'][$commentCount]['parent_created_at'] = $comment->parent_created_at;
            $article[0]['comments'][$commentCount]['author_slug'] = $comment->author_slug;
            $article[0]['comments'][$commentCount]['comment_path'] = $comment->path;
            $article[0]['comments'][$commentCount]['commented_at'] = $comment->created;

            ++$commentCount;
        }
        
        return $article;
    }
}