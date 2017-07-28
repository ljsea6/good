<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReferidosLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referidos_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tercero_id');
            $table->bigInteger('old_father')->nullable();
            $table->bigInteger('new_father')->nullable();
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
        Schema::drop('referidos_logs');
    }
}
