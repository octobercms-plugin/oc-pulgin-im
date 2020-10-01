<?php

namespace Jcc\Im\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        //todo 适配不同平台的数据结构

        return [
            'id'   => $this->id,
            'user_id'   => $this->user_id,
            'groupname' => $this->groupname,
            'list'=>UserResource::collection($this->whenLoaded('users'))

        ];
    }
}
