<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersAddFechaenvioiFechaenvionFechaentregadoBitacora extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('fecha_envio_i')->nullable();
            $table->timestamp('fecha_envio_n')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->json('bitacora')->nullable();
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
