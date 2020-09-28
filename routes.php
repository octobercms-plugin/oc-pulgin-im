<?php

Route::namespace('Jcc\Im\Http\Controllers')->prefix('/api/v1/im')->group(function () {

    Route::middleware(['api.jwt.refresh'])->group(function () {
        Route::post('initData', 'ImController@initData')->name('im.initData')->defaults('desc', '初始信息');
        Route::post('bind', 'ImController@bind')->name('im.bind')->defaults('desc', '绑定用户到wbsocket服务');
        Route::post('send', 'ImController@send')->name('im.send')->defaults('desc', '发送信息');
    });




});
