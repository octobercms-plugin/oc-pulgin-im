<?php namespace Jcc\Im\Events;


class ChatEventHandler
{


    public function chatRecords($user, $data)
    {
        return null; //todo
        return [
            "id"               => $data->id,
            "type"             => $data->type,
            "chat_source_type" => $data->chat_source_type,
            "content_type"     => $data->type,
            "from_id"          => $data->from_id,
            "from_avatar"      => $data->from_avatar,
            "content"          => $data->content,
            'mine'             => $user->id == $data->send_id ? true : false,
            "send_time"        => (string)$data->created_at,
            'extra'            => $data->extra
        ];
    }

    public function subscribe($events)
    {
        $events->listen('jcc.im.chatRecords', 'Jcc\Im\Events\ChatEventHandler@beforeBind');

    }
}
