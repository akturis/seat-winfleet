<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('winfleet_awards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operation_id')->unsigned();
            $table->integer('place')->unsigned();
            $table->bigInteger('user_id');
            $table->bigInteger('character_id');
            $table->enum('status', ['win', 'paid']);
            $table->foreign('operation_id')
                ->references('id')
                ->on('calendar_operations')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
            $table->foreign('character_id')
                ->references('character_id')
                ->on('character_infos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('winfleet_awards');
    }
}
