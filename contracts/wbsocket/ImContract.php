<?php

namespace Jcc\Im\Contracts\Wbsocket;

interface ImContract
{

    public function bind($array);//绑定用户
    public function send($array);//发送信息
    public function sendToUid($array);//发送信息
    public function sendToGroup($array);//发送信息


}
