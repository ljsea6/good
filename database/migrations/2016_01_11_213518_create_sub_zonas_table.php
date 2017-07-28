<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubZonasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('subzonas', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nombre');
            $table->integer('capacidad');
            $table->integer('zona_id')->unsigned();
            $table->foreign('zona_id')->references('id')->on('zonas')->onDelete('cascade');

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
        Schema::drop('subzonas');
    }
}
