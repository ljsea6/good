<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFacturasTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('facturas', function (Blueprint $table) {
            $table->increments('id');

            $table->text('numero');
            $table->integer('valor');
            $table->integer('iva');
            $table->integer('descuento');

            $table->integer('resolucion_id')->unsigned();
            $table->foreign('resolucion_id')->references('id')->on('resoluciones');

            $table->integer('cliente_id')->unsigned();
            $table->foreign('cliente_id')->references('id')->on('terceros');

            $table->integer('recogida_id')->unsigned();
            $table->foreign('recogida_id')->references('id')->on('recogidas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('facturas');
    }
}
