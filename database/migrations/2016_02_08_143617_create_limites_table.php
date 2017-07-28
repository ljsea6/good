<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLimitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('limites', function (Blueprint $table) 
        {
            $table->increments('id');

            $table->integer('subzona_id')->nullable()->unsigned();
            $table->foreign('subzona_id')->references('id')->on('subzonas');
            $table->integer('calle_inicio');
            $table->integer('calle_fin');
            $table->integer('carrera_inicio');
            $table->integer('carrera_fin');
            $table->text('cardinal');
            $table->text('ac1');
            $table->text('ac2');
            $table->text('ak1');
            $table->text('ak2');

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
    }
}
