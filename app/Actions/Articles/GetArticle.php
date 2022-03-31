<?php

namespace App\Actions\Articles;

use App\Models\Article;

class GetArticle
{
    public function __construct(CompileArticle $compileArticle)
    {
        $this->compileArticle = $compileArticle;
    }

    public function handle($articleId) : array
    {
        $rawArticle = Article::where('articles.id', $articleId)
        ->leftJoin('tags', function ($join) {
            $join->on('articles.id', '=', 'tags.tagable_id');
        })
        ->leftJoin('approvals', function ($join) {
            $join->on('approvalable_id', '=', 'articles.id');
        })
        ->leftJoin('comments', function ($join) {
            $join->on('commentable_id', '=', 'articles.id');
        })
        ->leftJoin('approvals as Ap1', function ($join) {
            $join->on('Ap1.approvalable_id', '=', 'comments.id');
        })
        ->join('users AS Us1', function ($join) {
            $join->on('Us1.id', '=', 'articles.user_id');
        })
        ->join('images AS Im1', function ($join) {
            $join->on('Us1.id', '=', 'Im1.imageable_id')
            ->where('Im1.format', 'profile');
        })
        ->select([
            'articles.id as id',
            'articles.title as title',
            'articles.body as body',
            'articles.created_at as created_at',
            'articles.synopsis as synopsis',
            'articles.slug as slug',
            'approvals.id as approval_id',
            'approvals.user_id as approval_user_id',
            'Ap1.id as comment_approval_id',
            'Ap1.approvalable_id as approved_comment_id',
            'Ap1.user_id as comment_approval_user_id',
            'tags.name as tag',
            'tags.id as tag_id',
            'Us1.name as name',
            'Us1.surname as surname',
            'Us1.slug as userslug',
            'Im1.path as path',
        ])
        ->groupBy([
            'articles.id',
            'tags.name',
            'tags.id',
            'Us1.name',
            'Us1.surname',
            'Us1.slug',
            'Im1.path',
            'approvals.id',
            'Ap1.id'
        ])
        ->get();

        return $this->compileArticle->compileArticle($rawArticle);
    }
}