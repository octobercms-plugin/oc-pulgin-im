<?php namespace Jcc\Im\http\Controllers;

use Event;
use Jcc\Jwt\http\Controllers\Controller;
use Jcc\Im\http\Requests\ImRequest;
use Jcc\Im\Models\Settings;

class ImController extends Controller
{

    /**
     * @param ImRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function initData(ImRequest $request)
    {
        /**
         * 拉取用户的好友列表和群友列表，在客户端连接wbsocket之前就可以拉。也可以和下方的bind方法放在一起。这里区分开，只初始化相关信息。
         *
         */
        $data = app()->make(\Jcc\Im\Contracts\Wbsocket\ImContract::class)->initData();
        return $this->response->success($data, 'ok');
    }

    /**
     * 绑定到wbsocket服务
     * @param ImRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function bind(ImRequest $request)
    {
        $data = $request->validationData();

        /**
         *  plugins/jcc/im/events/ImEventHandler.php  beforeBind  方法 中可更改 $data 数据，适配不同平台的绑定
         *
         * 或者在 plugins/xxx/xxx/Plugin.php 中返回false 就会不执行 上方 beforeBind。可自定义绑定前的操作
         * Event::listen('jcc.im.beforeBind',function (&$data){
         * return false;
         * },900000);
         *
         */
        if (Settings::get('support_before_bind', true)) {
            Event::fire('jcc.im.beforeBind', [&$data]);
        }


        $im = app()->make(\Jcc\Im\Contracts\Wbsocket\ImContract::class);
        $im->bind($data);

        /**
         * plugins/jcc/im/events/ImEventHandler.php afterBind 方法 绑定之后推送未读消息及其他消息
         *
         * * 或者在 plugins/xxx/xxx/Plugin.php 中返回false 就会不执行 上方 afterBind,可自定义绑定后的操作
         * Event::listen('jcc.im.afterBind',function (&$data){
         * return false;
         * },900000);
         *
         */
        if (Settings::get('support_after_bind', true)) {
            Event::fire('jcc.im.afterBind', [$data]);
        }

        return $this->response->success([], 'ok');
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
