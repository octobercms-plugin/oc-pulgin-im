<?php

Route::namespace('Jcc\Im\Http\Controllers')->prefix('/api/v1/im')->group(function () {

        Route::middleware(['api.jwt.refresh'])->group(function () {
            Route::get('initData', 'ImController@initData')->name('im.initData')->defaults('desc', '初始信息');
            Route::post('bind', 'ImController@bind')->name('im.bind')->defaults('desc', '绑定用户到wbsocket服务');
            Route::post('send', 'ImController@send')->name('im.send')->defaults('desc', '发送信息');

            Route::get('chatRecords', 'ChatController@chatRecords')->defaults('desc', '聊天记录');
        });
    });
