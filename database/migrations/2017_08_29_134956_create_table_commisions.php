<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCommisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tercero_id');
            $table->json('gift_card');
            $table->json('orders');
            $table->double('value');
            $table->json('bitacora');
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
        Schema::drop('commisions');
    }
}
