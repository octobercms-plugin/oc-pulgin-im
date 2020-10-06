<?php

namespace Jcc\Im\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Jcc\Im\Models\Settings;

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
        $user = auth('api')->user();

        if (Settings::get('transform_chatRecords', false)) {
            $data = \Event::fire('jcc.im.chatRecords', [$user, $this]);
            if (!empty($data)) {
                return $data;
            }
        }
        return [
            "id"               => $this->id,
            "type"             => $this->type,
            "chat_source_type" => $this->chat_source_type,
            "content_type"     => $this->type,
            "from_id"          => $this->from_id,
            "from_avatar"      => $this->from_avatar,
            "content"          => $this->content,
            'mine'             => $user->id == $this->send_id ? true : false,
            "send_time"        => (string)$this->created_at,
            'extra'            => $this->extra
        ];
    }
}
