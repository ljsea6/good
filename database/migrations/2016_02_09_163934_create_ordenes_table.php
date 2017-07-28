<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdenesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->increments('id');

            $table->text('numero');
            $table->dateTime('fecha');
            $table->dateTime('impresion_inicio')->nullable();
            $table->dateTime('impresion_fin')->nullable();
            $table->dateTime('asignacion_zona_inicio')->nullable();
            $table->dateTime('asignacion_zona_fin')->nullable();
            $table->dateTime('cerrada')->nullable();;
            $table->integer('usuario_cerro_id')->unsigned()->nullable();
            $table->foreign('usuario_cerro_id')->references('id')->on('terceros');

            $table->integer('recogida_id')->unsigned()->nullable();
            $table->foreign('recogida_id')->references('id')->on('recogidas');

            $table->integer('cliente_id')->unsigned();
            $table->foreign('cliente_id')->references('id')->on('terceros');

            $table->integer('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->integer('responsable_id')->unsigned();
            $table->foreign('responsable_id')->references('id')->on('terceros');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('ordenes');
    }
}
