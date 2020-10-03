<?php namespace Jcc\Im\http\Controllers;

use Event;
use Jcc\Jwt\http\Controllers\Controller;
use Jcc\Im\http\Requests\ChatRequest;

class ChatController extends Controller
{

    public function chatRecords(ChatRequest $request)
    {
        $data = $request->validationData();
        Event::fire('jcc.im.beforeGetChatRecords', [$data]);
        $chatRecords = app()->make(\Jcc\Im\Contracts\Wbsocket\ChatContract::class)->chatRecords($data);
        Event::fire('jcc.im.afterGetChatRecords', [$chatRecords]);

        return $this->response->success($chatRecords, 'ok');
    }


}
