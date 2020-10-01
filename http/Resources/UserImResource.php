<?php

namespace Jcc\Im\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserImResource extends JsonResource
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
            'mine'   => [
                "username" => $this->username,
                "id"       => $this->id,
                "status"   => "online",
                "sign"     => $this->sign,
                "avatar"   => $this->whenLoaded('avatar', function () {
                    return $this->avatar->file_name;
                })
            ],
            'friend' => GroupTypeResource::collection($this->whenLoaded('group_types')),
            'group'  => GroupResource::collection($this->whenLoaded('groups'))
        ];
    }
}
