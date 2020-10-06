<?php namespace Jcc\Im\Events;

use GatewayClient\Gateway;
use Jcc\Im\Models\ChatRecord;
use Jcc\Im\Models\Group;
use Jcc\Im\Models\Settings;

class ImEventHandler
{
    public function beforeBind($data)
    {
        $data['user'] = auth('api')->user();
    }


    public function afterBind($data)
    {
        //做一些后置操作
        $user = $data['user'];
        $im   = app()->make(\Jcc\Im\Contracts\Wbsocket\ImContract::class);

        //获取未读记录
        $records = $user->getNoReadChatRecord();


        $records->each(function ($item) use ($user, $im) {
            $chatMessage = $item->content;
            if ($item->send_id == $user->id) {
                $chatMessage['content']['mine'] = true;
            }
            //发送未读信息
            $data['bind_user_id'] = $user->getBindImId();
            $data['content']      = $chatMessage;
            $im->sendToUid($data);
        });

        //聊天记录已读
        $user->clearNoReadChatRecord();
        //todo Gateway bind

        //未读消息盒子
        $msgs  = $user->getNoReadMsgboxs();
        $count = count($msgs);

        if ($count) {
            $data['type']    = 'msgBox';
            $data['content'] = $count;

            $sendData['bind_user_id'] = $user->getBindImId();
            $sendData['content']      = json_encode($data, JSON_UNESCAPED_UNICODE);
            $user->setNoReadMsgboxesReaded();
        }
    }

    public function beforeSend($data)
    {
    }

    public function sending($user, $data, $im)
    {

        $msg = ChatRecord::transform_msg($user, $data);//要发送的信息

        if ($data['type'] == 'group') {
            //是否记录群组消息
            if (Settings::get('group_chat_record', true)) {
                $group = Group::find($data['model_id']);
                //消息入库
                $data['chat_source_type'] = ChatRecord::CHAT_SOURCE_TYPE_GROUP;
                $record                   = $user->saveChatRecord($data);
                $record->if_read          = ChatRecord::IF_READ_1;
                $record->save();
                $msg['id'] = $record->id;
                //记录未读消息
                $users = $group->users()->get();
                $users->each(function ($item) use ($user, $record, $im) {
                    if ($im->isUidOnline($item->id)) {//在线用户不操作
                    } else {//不在线的群用户记录未读消息
                        $item->setNoReadChatRecordIds($record);
                    }
                });
            }
        } elseif ($data['type'] == 'friend') {
            $data['chat_source_type'] = ChatRecord::CHAT_SOURCE_TYPE_FRIEND;

            if ($im->isUidOnline($user->getBindImId())) {
                if (Settings::get('friend_chat_record', true)) {
                    $record          = $user->saveChatRecord($data);
                    $record->if_read = ChatRecord::IF_READ_1;
                    $record->save();
                    $msg['id'] = $record->id;

                }
            } else {
                if (Settings::get('friend_chat_record', true)) {//对方不在线是否记录聊天记录
                    $record          = $user->saveChatRecord($data);
                    $record->if_read = ChatRecord::IF_READ_0;
                    $record->save();
                    $msg['id'] = $record->id;

                }
                if (Settings::get('user_not_online_send_system', true)) {//对方不在线是否发送已离线的系统消息
                    $friend = \Jcc\jwt\Models\User::find($data['model_id']);
                    $key    = ChatRecord::user_not_online_send_system_key($friend->id);
                    if (!\Cache::has($key)) {
                        $data['chat_source_type'] = ChatRecord::CHAT_SOURCE_TYPE_FRIEND_SYSTEM_NOT_ONLINE;//消息来源为系统不在线
                        $data['content']['value'] =
                            ChatRecord::$chatSourceTypeMaps[ChatRecord::CHAT_SOURCE_TYPE_FRIEND_SYSTEM_NOT_ONLINE];
                        $data['content_type']     = ChatRecord::CONTENT_TYPE_TEXT;

                        //保存离线记录
                        $notOnlineRecord = $user->saveCharRecord($data);

                        //发送离线消息
                        $notOnlineMsg         = ChatRecord::transform_msg($user, $data);//不在线的发送的信息
                        $notOnlineMsg['id']   = $notOnlineRecord->id;
                        $data['bind_user_id'] = $user->getBindImId();
                        $data['content']      = $notOnlineMsg;
                        $im->sendToUid($data);

                        \Cache::put(
                            $key,
                            1,
                            now()->addMinutes(Settings::get('user_not_online_send_system_msg_times', 30))
                        );//用户不在线发送消息的频次
                    }
                }
            }
        }
        return $msg;
    }

    public function afterSend($data)
    {
    }

    public function subscribe($events)
    {
        $events->listen('jcc.im.beforeBind', 'Jcc\Im\Events\ImEventHandler@beforeBind');
        $events->listen('jcc.im.afterBind', 'Jcc\Im\Events\ImEventHandler@afterBind');

        $events->listen('jcc.im.beforeSend', 'Jcc\Im\Events\ImEventHandler@beforeSend');
        $events->listen('jcc.im.sending', 'Jcc\Im\EventsImEventHandler@sending');
        $events->listen('jcc.im.afterSend', 'Jcc\Im\Events\ImEventHandler@afterSend');
    }
}
