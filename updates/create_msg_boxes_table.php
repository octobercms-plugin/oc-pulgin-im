<?php namespace Jcc\Im\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateMsgBoxesTable extends Migration
{
    public function up()
    {
        Schema::create('jcc_im_msg_boxes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('uid')->comment('接收者id');
            $table->string('content', 255)->nullable()->comment('发送内容');
            $table->tinyInteger('from')->nullable()->comment('发送者id');
            $table->unsignedInteger('from_group')->default(0)->comment('消息从哪个群来的，可为空');
            $table->tinyInteger('read')->default(0)->comment('是否读');
            $table->string('type', 255)->nullable()->comment('好友消息或加群消息或系统消息：friend group system');
            $table->dateTime('receive_time')->nullable()->comment('接收时间');
            $table->string('state', 255)->nullable()->comment('同意或拒绝 agree refuse');
            $table->text('user')->nullable()->comment('冗余字段');
            $table->tinyInteger('if_push')->default(0)->comment('是否推送过消息');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jcc_im_msg_boxes');
    }
}
