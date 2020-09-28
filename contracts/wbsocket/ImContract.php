<?php

namespace Jcc\Im\Contracts\Wbsocket;

interface ImContract
{

    public function bind($array);
    public function send($array);

}
