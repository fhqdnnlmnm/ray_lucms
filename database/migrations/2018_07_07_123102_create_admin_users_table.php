<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 50)->default('');
            $table->string('email', 50)->default('');
            $table->string('password', 255)->default('');
            $table->enum('enable', ['T', 'F'])->default('F')->comment('启用状态：F禁用，T启用');
            $table->enum('is_admin', ['T', 'F'])->default('F')->comment('是否可登录后台：F否，是');
            $table->string('description', 255)->default('')->comment('一句话描述');
            $table->integer('head_image')->default(0)->comment('头像');
            $table->string('remember_token', 100)->default('');
            $table->timestamps();
            $table->unique('email');
            $table->index('head_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
}
