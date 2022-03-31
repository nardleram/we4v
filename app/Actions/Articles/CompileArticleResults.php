<?php

namespace App\Actions\Articles;

class CompileArticleResults
{
    public function handle($results)
    {
        $articles = [];
        $articleId = 0;
        $articleCount = 0;
        $tagCount = 0;
        $loop = 0;
        $articles[$articleCount]['tags'] = [];

        foreach ($results as $article) {
            if ($loop > 0 && $articleId !== $article->id) {
                ++$articleCount;
                $tagCount = 0;
                $articles[$articleCount]['tags'] = [];
            }

            $articles[$articleCount]['title'] = $article->title;
            $articles[$articleCount]['synopsis'] = $article->synopsis;
            $articles[$articleCount]['slug'] = $article->slug;

            //Tags
            $articles[$articleCount]['tags'][$tagCount]['tag'] = $article->tag;
            ++$tagCount;

            $articleId = $article->id;
        }
        
        return $articles;
    }
}