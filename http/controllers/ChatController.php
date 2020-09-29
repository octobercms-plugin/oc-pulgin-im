<?php namespace Jcc\Im\http\Controllers;

use Event;
use Jcc\Jwt\http\Controllers\Controller;
use Jcc\Im\http\Requests\ImRequest;

class ChatController extends Controller
{

    public function initData(ImRequest $request)
    {
        $data = app()->make(\Jcc\Im\Contracts\Wbsocket\ChatContract::class)->initData();
        Event::fire('jcc.im.initData', [&$data]);

        return $this->response->success($data, 'ok');

    }


    public function chatRecords(ImRequest $request)
    {
        $data = $request->validationData();
        Event::fire('jcc.im.beforeGetChatRecords', [&$data]);
        $chatRecords = app()->make(\Jcc\Im\Contracts\Wbsocket\ChatContract::class)->chatRecords($data);
        Event::fire('jcc.im.afterGetChatRecords', [$chatRecords]);

        return $this->response->success($chatRecords, 'ok');

    }



}
