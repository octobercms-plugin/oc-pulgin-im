<?php

namespace Jcc\Im\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            "id"        => $this->id,
            "groupname" => $this->groupname,
            "avatar"    => $this->avatar,
            "user_id"   => $this->user_id,
        ];
    }
}
