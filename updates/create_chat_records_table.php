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
            $table->unsignedInteger('from_id')->comment('发送者id');
            $table->string('from_avatar')->comment('发送者头像');
            $table->unsignedInteger('to_id')->default(0)->comment('接收者id');
            $table->string('to_avatar')->comment('接收者头像');
            $table->unsignedInteger('to_group_id')->nullable()->comment('群组id');
            $table->string('type', 255)->nullable()
                ->comment('friend 和好友聊的天，group和群友聊的天');
            $table->string('chat_source_type', 255)
                ->nullable()
                ->comment('消息来源类型 :friend 好友消息,group 群组消息，friend-system在好友聊天里发送的系统消息,group-system在群组里发送的系统消息，是一个冗余字段');
            $table->string('content_type', 255)->nullable()->comment('消息类型，文本，图片，文件，表情等');
            $table->text('content')->nullable()->comment('消息内容');
            $table->text('extra')->nullable()->comment('额外的内容');
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
