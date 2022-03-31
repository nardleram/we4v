<?php

namespace App\Actions\Articles;

use Carbon\Carbon;

class CompileArticles
{
    public function compileArticles($rawArticles) : array
    {
        $articles = [];
        $tags = [];
        $articleCount = 0;
        $tagCount = 0;
        $currentArticleId = 0;
        $currentTagId = 0;
        $loop = 0;

        foreach ($rawArticles as $rawArticle) {
            if ($loop > 0 && $rawArticle->id !== $currentArticleId) {
                ++$articleCount;
                $tags = [];
            }
            if ($loop > 0 && $rawArticle->tag_id !== $currentTagId) {
                ++$tagCount;
            }

            //Gather article data
            if ($currentArticleId !== $rawArticle->id) {
                $articles[$articleCount]['created_at'] = Carbon::parse($rawArticle->created_at)->format('D j M Y, H:i');
                $articles[$articleCount]['slug'] = $rawArticle->slug;
                $articles[$articleCount]['synopsis'] = $rawArticle->synopsis;
                $articles[$articleCount]['title'] = $rawArticle->title;
            }

            // Tags
            if (!in_array($rawArticle->tag, $tags)) {
                array_push($tags, $rawArticle->tag);
                $articles[$articleCount]['tags'][$tagCount]['tag'] = $rawArticle->tag;
            }

            $currentTagId = $rawArticle->tag_id;
            $currentArticleId = $rawArticle->id;
            ++$loop;
        }

        return $articles;
    }
}