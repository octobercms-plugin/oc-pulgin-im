<?php

namespace Jcc\Im\Contracts\Wbsocket;

interface ImContract
{
    public function initData();//初始化信息好友群组等信息
    public function bind($array);//绑定用户
    public function send($array);//发送信息
    public function sendToUid($array);//发送信息
    public function sendToGroup($array);//发送信息

}
