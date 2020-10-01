<?php

namespace Jcc\Im\Contracts\Wbsocket;

interface ChatContract
{


    public function members($data);//群成员列表

    public function chatRecords($data);//聊天记录

    //todo 下面的可通过分离到另外一个插件提供
    public function createGroup($data);//创建群

    public function applyGroup($data);//申请入群

    public function agreeGroup($data);//同意入群

    public function refuseGroup($data);//拒绝入群

    public function applyFriend($data);//申请好友

    public function agreeFriend($data);//同意好友

    public function refuseFriend($data);//拒绝好友

    public function msgbox($data);//用户的消息盒子

    public function search($data);//搜索好友或群


}
