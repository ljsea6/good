<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTiposTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tipos', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nombre');
            $table->integer('padre_id')->nullable();
            $table->foreign('padre_id')->references('id')->on('tipos');
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
        Schema::drop('tipos');
    }
}
