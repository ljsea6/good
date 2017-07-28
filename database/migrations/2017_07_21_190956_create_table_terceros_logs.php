<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTercerosLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terceros_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('tercero_id');
            $table->bigInteger('padre_id')->nullable();
            $table->string('user');
            $table->string('ip');
            $table->string('browser');
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
        Schema::drop('terceros_logs');
    }
}
