<?php namespace Jcc\Im\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateGroupTypesTable extends Migration
{
    public function up()
    {
        Schema::create('jcc_im_group_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('groupname', 255)->comment('用户组名称');
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jcc_im_group_types');
    }
}
