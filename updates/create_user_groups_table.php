<?php namespace Jcc\Im\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateUserGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('jcc_im_user_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('group_id');
            $table->timestamp('join_time')->nullable()->comment('加入时间');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jcc_im_user_groups');
    }
}
