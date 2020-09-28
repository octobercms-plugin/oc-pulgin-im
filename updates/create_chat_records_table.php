<?php namespace Jcc\Im\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateChatRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('jcc_im_chat_records', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('send_id')->comment('发送者id');
            $table->unsignedInteger('receive_id')->nullable()->comment('接收者id');
            $table->unsignedInteger('group_id')->nullable()->comment('群组id');
            $table->string('type', 255)->nullable()->comment('消息类型 :friend 好友消息,group 群组消息');
            $table->text('content')->nullable()->comment('消息内容');
            $table->tinyInteger('if_read')->default(0)->comment('是否读');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jcc_im_chat_records');
    }
}
