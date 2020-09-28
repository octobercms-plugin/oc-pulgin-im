<?php namespace Jcc\Im\http\Controllers;

use Event;
use Jcc\Jwt\http\Controllers\Controller;
use Jcc\Im\http\Requests\ImRequest;

class ImController extends Controller
{
    public function bind(ImRequest $request)
    {
        $data = $request->validationData();

        Event::fire('jcc.im.beforeBind', [$data]);

        app()->make(\Jcc\Im\Contracts\Wbsocket\ImContract::class)->bind($data);

        Event::fire('jcc.im.binded', [$data]);

        return $this->response->success([], 'ok');
    }

    public function send(ImRequest $request)
    {
        $data = $request->validationData();
        Event::fire('jcc.im.beforeSend', [$data]);
        app()->make(\Jcc\Im\Contracts\Wbsocket\ImContract::class)->send($data);
        Event::fire('jcc.im.sended', [$data]);
        return $this->response->success([], 'ok');

    }


}
