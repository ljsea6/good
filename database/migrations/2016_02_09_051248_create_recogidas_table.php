<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecogidasTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('recogidas', function (Blueprint $table) {
            $table->increments('id');

            $table->text('numero');
            $table->timestamp('fecha');
            $table->timestamp('ingreso')->nullable();
            $table->text('observaciones');

            $table->integer('cliente_id')->unsigned();
            $table->foreign('cliente_id')->references('id')->on('terceros');

            $table->integer('mensajero_id')->unsigned();
            $table->foreign('mensajero_id')->references('id')->on('terceros');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('recogidas');
    }
}
