<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecogidaDetallesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('recogidas_detalle', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('cantidad');
            $table->integer('valor');
            $table->integer('peso');

            $table->integer('recogida_id')->unsigned();
            $table->foreign('recogida_id')->references('id')->on('recogidas');

            $table->integer('tipo_servicio_id')->unsigned();
            $table->foreign('tipo_servicio_id')->references('id')->on('tipos');

            $table->integer('tipo_tiempo_id')->unsigned();
            $table->foreign('tipo_tiempo_id')->references('id')->on('tipos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('recogida_detalles');
    }
}
