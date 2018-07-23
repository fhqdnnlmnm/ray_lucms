<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    public function toArray($request)
    {

        $collection = $this->collection;
        $collection->each(function ($info) {
        });
        return [
            'data' => $collection
        ];

    }
}
