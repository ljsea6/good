<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTercerosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('terceros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('identificacion');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('direccion')->nullable();
            $table->string('telefono');
            $table->string('email');
            $table->text('avatar')->nullable();
            $table->string('usuario')->nullable();
            $table->string('contraseña', 60)->nullable();
            $table->string('perfil')->nullable();

            $table->integer('tipo_id')->unsigned();
            $table->foreign('tipo_id')->references('id')->on('tipos');

            $table->integer('rol_id')->unsigned()->nullable();
            $table->foreign('rol_id')->references('id')->on('roles');

            $table->integer('ciudad_id')->unsigned();
            $table->foreign('ciudad_id')->references('id')->on('ciudades');

            $table->integer('cargo_id')->nullable()->unsigned();
            $table->foreign('cargo_id')->references('id')->on('cargos');

            $table->integer('sucursal_id')->nullable()->unsigned();
            $table->foreign('sucursal_id')->references('id')->on('sucursales');

            $table->integer('resolucion_id')->nullable()->unsigned();
            $table->foreign('resolucion_id')->references('id')->on('resoluciones');

            $table->integer('zona_id')->nullable()->unsigned();
            $table->foreign('zona_id')->references('id')->on('zonas');

            $table->integer('sector_id')->nullable()->unsigned();
            $table->foreign('sector_id')->references('id')->on('sectores');

            $table->integer('padre_id')->nullable()->unsigned();
            $table->foreign('padre_id')->references('id')->on('terceros');

            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('terceros');
    }
}
