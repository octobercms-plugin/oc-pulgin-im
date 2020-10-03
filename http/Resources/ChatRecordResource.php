<?php

namespace Jcc\Im\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user    = auth('api')->user();
        $content = $this->content;
        if ($user) {
            if ($user->id == $this->send_id) {
                $content['mine'] = true;
            }
        }

        return [
            "id"        => $this->id,
            "type"      => $this->type,
            "send_id"   => $this->send_id,
            "content"   => $content,
            "send_time" => (string)$this->created_at,
        ];
    }
}
