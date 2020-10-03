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

    public function sending($user, $msg, $data, $im)
    {

        if ($data['type'] == 'group') {
            //是否记录群组消息
            if (Settings::get('group_chat_record', true)) {
                $group = Group::find($data['model_id']);
                //消息入库
                $record           = new ChatRecord();
                $record->type     = ChatRecord::TYPE_GROUP;
                $record->send_id  = $user->id;
                $record->group_id = $data['model_id'];
                $record->content  = json_encode($msg, JSON_UNESCAPED_UNICODE);
                $record->if_read  = ChatRecord::IF_READ_1;
                $record->save();

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
            $friend             = \Jcc\jwt\Models\User::find($data['model_id']);
            $record             = new ChatRecord();
            $record->type       = ChatRecord::TYPE_FRIEND;
            $record->send_id    = $user->id;
            $record->receive_id = $data['model_id'];
            $record->content    = json_encode($msg, JSON_UNESCAPED_UNICODE);
            if ($im->isUidOnline($user->getBindImId())) {
                if (Settings::get('friend_chat_record', true)) {
                    $record->if_read = ChatRecord::IF_READ_1;
                    $record->save();
                }
            } else {
                if (Settings::get('friend_chat_record', true)) {//对方不在线是否记录聊天记录
                    $record->if_read = ChatRecord::IF_READ_0;
                    $record->save();
                }
                if (Settings::get('user_not_online_send_system', true)) {//对方不在线是否发送已离线的系统消息
                    $key = ChatRecord::user_not_online_send_system_key($friend->id);
                    if (!\Cache::has($key)) {
                        $msg = ChatRecord::msg(
                            $user,
                            [
                                'content' => [
                                    'type'    => 'text',
                                    'content' =>  '对方已离线...'
                                ]
                            ],
                            'friend-system'
                        );

                        $record          = new ChatRecord();
                        $record->type    = ChatRecord::MSG_TYPE_FRIEND_SYSTEM;
                        $record->send_id = $user->id;

                        $record->content = json_encode($msg, JSON_UNESCAPED_UNICODE);
                        $record->ssave();

                        $data['bind_user_id'] = $user->getBindImId();
                        $data['content']      = $msg;
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
    }

    public function afterSend($data)
    {
    }

    public function subscribe($events)
    {
        $events->listen('jcc.im.beforeBind', 'ImEventHandler@beforeBind');
        $events->listen('jcc.im.afterBind', 'ImEventHandler@afterBind');

        $events->listen('jcc.im.beforeSend', 'ImEventHandler@beforeSend');
        $events->listen('jcc.im.sending', 'ImEventHandler@sending');
        $events->listen('jcc.im.afterSend', 'ImEventHandler@afterSend');
    }
}
