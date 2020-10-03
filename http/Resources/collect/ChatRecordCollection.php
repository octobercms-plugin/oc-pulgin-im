<?php

namespace Jcc\Im\Http\Resources\Collect;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Jcc\Im\Http\Resources\ChatRecordResource;

class ChatRecordCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => ChatRecordResource::collection($this->collection)
        ];
    }
}
