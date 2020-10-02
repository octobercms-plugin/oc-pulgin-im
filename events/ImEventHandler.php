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
            if ($im->isUidOnline($user->getBindImId())) {
                if (Settings::get('friend_chat_record', true)) {
                    $record             = new ChatRecord();
                    $record->type       = ChatRecord::TYPE_FRIEND;
                    $record->send_id    = $user->id;
                    $record->receive_id = $data['model_id'];
                    $record->content    = json_encode($msg, JSON_UNESCAPED_UNICODE);
                    $record->if_read    = ChatRecord::IF_READ_1;
                    $record->save();
                }
            } else {
                $record             = new ChatRecord();
                $record->type       = ChatRecord::TYPE_FRIEND;
                $record->send_id    = $user->id;
                $record->receive_id = $data['model_id'];
                $record->content    = json_encode($msg, JSON_UNESCAPED_UNICODE);
                $record->if_read    = ChatRecord::IF_READ_0;
                $record->save();
                //todo 是否告诉自己对方不在线
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
