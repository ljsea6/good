<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTercerosPermisos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terceros_permisos', function (Blueprint $table) 
        {
            $table->increments('id');

            $table->integer('usuario_id')->nullable()->unsigned();
            $table->foreign('usuario_id')->references('id')->on('terceros');

            $table->integer('permiso_id')->nullable()->unsigned();
            $table->foreign('permiso_id')->references('id')->on('permisos');

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
        Schema::drop('terceros_permisos');
    }
}
