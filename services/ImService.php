<?php namespace Jcc\Im\Services;

use Jcc\Im\Contracts\Wbsocket\ImContract;
use GatewayClient\Gateway;
use Jcc\Im\Http\Resources\UserImResource;
use Jcc\Im\Models\Settings;
use Jcc\Im\Models\ChatRecord;
use Jcc\Im\Models\Group;

class ImService implements ImContract
{
    public function __construct()
    {
        Gateway::$registerAddress = Settings::get('gateway_register_address', '127.0.0.1:1238');
    }

    /**
     * 初始化信息
     * @return UserImResource|void
     */
    public function initData()
    {
        $user = auth('api')->user();
//        $user = \Jcc\jwt\Models\User::first();
        $user->load('avatar');
        $user->load('group_types.users');
        $user->load('im_groups.users');
        return new UserImResource($user);
    }

    public function bind($data)
    {
        $user = auth('api')->user();

        //绑定单用户
        Gateway::bindUid($data['client_id'], $user->getBindImId());

        //群绑定
        $user->groups()->get()->each(function ($item) use ($data) {
            Gateway::joinGroup($data['client_id'], $item->getBindImId());
        });
    }

    public function send($data)
    {
        $user = auth('api')->user();
        switch ($data['type']) {
            case 'group':
                $group = Group::find($data['model_id']);

                $msg = \Event::fire('jcc.im.sending', [$user, $data, $this]);


                $sendData['bind_group_id'] = $group->getBindImId();
                $sendData['content']       = json_encode($msg, JSON_UNESCAPED_UNICODE);
                $sendData['bind_user_id']  = $user->getBindImId();
                $this->sendToGroup($sendData);

                break;
            case 'friend':
                //要发送的信息


                $msg = \Event::fire('jcc.im.sending', [$user, $data, $this]);

                $sendData['bind_user_id'] = $user->getBindImId();
                $sendData['content']      = json_encode($msg, JSON_UNESCAPED_UNICODE);
                $this->sendToUid($sendData);
                break;
            default:
                break;
        }
    }

    public function sendToUid($data)
    {
        Gateway::sendToUid($data['bind_user_id'], $data['content']);
    }

    public function isUidOnline($id)
    {
        return Gateway::isUidOnline($id);
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
