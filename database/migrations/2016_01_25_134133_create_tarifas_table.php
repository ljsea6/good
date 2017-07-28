<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarifasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tarifas', function (Blueprint $table) 
        {
            $table->increments('id');

            $table->integer('tipo_mensajeria_id')->nullable()->unsigned();
            $table->foreign('tipo_mensajeria_id')->references('id')->on('tipos');

            $table->integer('tipo_tiempo_id')->nullable()->unsigned();
            $table->foreign('tipo_tiempo_id')->references('id')->on('tipos');
            
            $table->integer('tipo_ciudad_id')->nullable()->unsigned();
            $table->foreign('tipo_ciudad_id')->references('id')->on('tipos');

            $table->integer('tipo_producto_id')->nullable()->unsigned();
            $table->foreign('tipo_producto_id')->references('id')->on('tipos');

            $table->integer('cantidad_desde')->nullable()->unsigned();
            $table->integer('cantidad_hasta')->nullable()->unsigned();

            $table->integer('peso_desde')->nullable()->unsigned();
            $table->integer('peso_hasta')->nullable()->unsigned();

            $table->integer('valor')->nullable()->unsigned();

            $table->boolean('estado')->default(1);      

            $table->softDeletes();
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
        //
         Schema::drop('tarifas');
    }
}