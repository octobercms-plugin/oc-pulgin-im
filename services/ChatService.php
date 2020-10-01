<?php namespace Jcc\Im\Services;

use Jcc\Im\Http\Resources\UserImResource;

class ChatService extends AbstractChatService
{

    public function initData()
    {
        $user = auth('api')->user();
//        $user = \Jcc\jwt\Models\User::first();
        $user->load('avatar');
        $user->load('group_types.users');
        $user->load('groups.users');
        return  new UserImResource($user);
    }

    public function members($data)
    {
        // TODO: Implement members() method.
    }

    public function chatRecords($data)
    {
        // TODO: Implement chatRecords() method.
    }

}
