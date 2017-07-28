<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesPermisos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_permisos', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->integer('rol_id')->nullable()->unsigned();
            $table->foreign('rol_id')->references('id')->on('roles');
            $table->integer('permiso_id')->nullable()->unsigned();
            $table->foreign('permiso_id')->references('id')->on('permisos');
            $table->boolean('ver')->default(0);
            $table->boolean('crear')->default(0);
            $table->boolean('editar')->default(0);
            $table->boolean('borrar')->default(0);

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
        Schema::drop('roles_permisos');
    }
}
