<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBelongGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('belong_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('belong_groups');
    }
}
