<?php namespace Jcc\Im\Services;

use Jcc\Im\Contracts\Wbsocket\ImContract;

class ImService implements ImContract
{
    public function bind($data)
    {
        //todo Gateway bind

    }

    public function send($data)
    {
        //todo Gateway sending

        Event::fire('jcc.im.sending', [$data]); //todo 发送过程中入库

    }

}
