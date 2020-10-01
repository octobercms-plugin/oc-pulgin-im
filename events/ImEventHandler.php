<?php namespace Jcc\Im\Events;

use GatewayClient\Gateway;
use Jcc\Im\Models\ChatRecord;

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
            Gateway::sendToUid($user->id, json_encode($data, JSON_UNESCAPED_UNICODE));
            $user->setNoReadMsgboxesReaded();
        }
    }

    public function beforeSend($data)
    {
    }

    public function afterSend($data)
    {
    }

    public function subscribe($events)
    {
        $events->listen('jcc.im.beforeBind', 'ImEventHandler@beforeBind');
        $events->listen('jcc.im.afterBind', 'ImEventHandler@afterBind');

        $events->listen('jcc.im.beforeSend', 'ImEventHandler@beforeSend');
        $events->listen('jcc.im.afterSend', 'ImEventHandler@afterSend');
    }

}
