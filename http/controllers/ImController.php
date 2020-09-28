<?php namespace Jcc\Im\http\Controllers;

use Event;
use Jcc\Jwt\http\Controllers\Controller;
use Jcc\Im\http\Requests\ImRequest;

class ImController extends Controller
{


    /**
     * 绑定到wbsocket服务
     * @param ImRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function bind(ImRequest $request)
    {
        $data = $request->validationData();

        Event::fire('jcc.im.beforeBind', [&$data]);

        $im = app()->make(\Jcc\Im\Contracts\Wbsocket\ImContract::class);
        $im->bind($data);

        Event::fire('jcc.im.afterBind', [&$data]); //todo


        return $this->response->success($data, 'ok');
    }


    /**
     * 发送信息
     * @param ImRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function send(ImRequest $request)
    {
        $data = $request->validationData();
        Event::fire('jcc.im.beforeSend', [$data]);
        app()->make(\Jcc\Im\Contracts\Wbsocket\ImContract::class)->send($data);
        Event::fire('jcc.im.afterSend', [$data]);
        return $this->response->success([], 'ok');

    }



}
