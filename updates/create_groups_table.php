<?php namespace Jcc\Im\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('jcc_im_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('groupname', 255)->nullable()->comment('群组名字');
            $table->string('avatar', 255)->nullable()->comment('群组头像');
            $table->unsignedInteger('user_id')->nullable()->comment('谁创建的');
            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jcc_im_groups');
    }
}
