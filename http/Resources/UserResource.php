<?php

namespace Jcc\Im\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "username" => $this->username,
            "id"       => $this->id,
            "status"   => "online",
            "sign"     => $this->sign,
            "avatar"   => $this->whenLoaded('avatar', function () {
                return $this->avatar->file_name;
            })
        ];
    }
}
