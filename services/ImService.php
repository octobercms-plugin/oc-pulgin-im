<?php namespace Jcc\Im\Services;

use Jcc\Im\Contracts\Wbsocket\ImContract;
use GatewayClient\Gateway;
use Jcc\Im\Models\Settings;

class ImService implements ImContract
{
    public function __construct()
    {
        Gateway::$registerAddress = Settings::get('gateway_register_address', '127.0.0.1:1238');
    }

    public function bind($data)
    {
        $user = auth('api')->user();

        //绑定单用户
        Gateway::bindUid($data['client_id'], $user->id);

        //群绑定
        $user->groups()->get()->each(function ($item) use ($data) {
            Gateway::joinGroup($data['client_id'], $item->id);
        });
    }

    public function send($data)
    {
        //todo Gateway sending

        Event::fire('jcc.im.sending', [$data]); //todo 发送过程中入库
    }

    public function sendToUid($data)
    {
        Gateway::sendToUid($data['bind_user_id'], $data['content']);
    }

    public function sendToGroup($data)
    {
        Gateway::sendToGroup(
            $data['bind_group_id'],
            $data['content'],
            Gateway::getClientIdByUid($data['bind_user_id'])
        );
    }
}
