<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',191)->unique();
            $table->string('email',191)->unique();
            $table->string('password');
            $table->integer('type')->default(1);
            $table->rememberToken();
            $table->integer('per_department_create')->default(0);
            $table->integer('per_department_update')->default(0);
            $table->integer('per_department_delete')->default(0);
            $table->integer('per_group_create')->default(0);
            $table->integer('per_group_update')->default(0);
            $table->integer('per_group_delete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
