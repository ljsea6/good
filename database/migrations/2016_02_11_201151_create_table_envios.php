<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEnvios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('envios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idenvio');
            $table->text('direccion');
            $table->text('telefono');
            $table->text('destinatario');
            $table->integer('zona_id')->unsigned();
            $table->foreign('zona_id')->references('id')->on('zonas');
            $table->integer('subzona_id')->unsigned();
            $table->foreign('subzona_id')->references('id')->on('subzonas');
            $table->integer('orden_id')->unsigned();
            $table->foreign('orden_id')->references('id')->on('ordenes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('envios');
    }
}
