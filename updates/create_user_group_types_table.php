<?php namespace Jcc\Im\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateUserGroupTypesTable extends Migration
{
    public function up()
    {
        Schema::create('jcc_im_user_group_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('group_type_id');
            $table->timestamp('join_time')->nullable()->comment('成为好友时间');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jcc_im_user_group_types');
    }
}
