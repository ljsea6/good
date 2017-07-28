<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSucursalesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nombre');
            $table->string('direccion');
            $table->string('telefono');
            $table->string('email');
            $table->integer('resolucion_credito_id')->unsigned();
            $table->foreign('resolucion_credito_id')->references('id')->on('resoluciones');
            $table->integer('resolucion_contado_id')->unsigned();
            $table->foreign('resolucion_contado_id')->references('id')->on('resoluciones');
            $table->integer('ciudad_id')->unsigned();
            $table->foreign('ciudad_id')->references('id')->on('ciudades');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('sucursales');
    }
}
