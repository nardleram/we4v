<?php

namespace App\Actions\Tags;

use App\Models\Tag;

class StoreTags
{
    public function handle($request, $parentId)
    {
        foreach ($request->tags as $tag) {
            Tag::create([
                'name' => $tag,
                'tagable_id' => $parentId,
                'tagable_type' => $request->tagable_type
            ]);
        }
    }
}