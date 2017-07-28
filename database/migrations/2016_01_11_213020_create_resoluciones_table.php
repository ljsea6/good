<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResolucionesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('resoluciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->text('prefijo')->nullable;
            $table->integer('numero');
            $table->integer('digitos');           
            $table->dateTime('vencimiento');
            $table->text('desde');
            $table->text('hasta');
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
        Schema::drop('resoluciones');
    }
}
