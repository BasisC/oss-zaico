<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBelongDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('belong_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('department_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('per_taget')->default(0);
            $table->integer('per_belong')->default(0);
            $table->integer('per_stock_inspection')->default(0);
            $table->integer('per_stock_update')->default(0);
            $table->integer('per_stock_delete')->default(0);
            $table->integer('per_stock_ext')->default(0);
            $table->integer('per_class_create')->default(0);
            $table->integer('per_class_update')->default(0);
            $table->integer('per_class_delete')->default(0);
            $table->timestamps();
            
             $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('restrict')
                ->onUpdate('restrict');
                
             $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('belong_departments');
    }
}
