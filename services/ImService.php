<?php namespace Jcc\Im\Services;

use Jcc\Im\Contracts\Wbsocket\ImContract;
use GatewayClient\Gateway;
use Jcc\Im\Http\Resources\UserImResource;
use Jcc\Im\Models\Settings;
use Jcc\Im\Models\ChatRecord;

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
        $user->load('groups.users');
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
        //todo Gateway sending
        $user = auth('api')->user();
        switch ($data['type']) {
            case 'group':
                $msg['username']  = $user->username;
                $msg['avatar']    = $user->avatar->path;
                $msg['id']        = $user->id;
                $msg['timestamp'] = time() * 1000;
                $msg['content']   = $data['content'];
                $msg['type']      = 'group';

                $group = Group::find($data['model_id']);

                if (Settings::get('chat_record', true)) {
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
                    $users->each(function ($item) use ($user, $record) {

                        if (!Gateway::isUidOnline($item->id)) {//在线用户不操作
                        } else {//不在线的群用户记录未读消息
                            $item->setNoReadChatRecordIds($record);
                        }
                    });
                }
                Gateway::sendToGroup(
                    $group->getBindImId(),
                    json_encode($msg, JSON_UNESCAPED_UNICODE),
                    Gateway::getClientIdByUid($user->getBindImId())
                );
                break;
            case 'friend':
                break;
            default:
                break;
        }

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
