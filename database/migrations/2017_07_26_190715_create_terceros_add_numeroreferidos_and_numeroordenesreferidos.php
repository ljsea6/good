<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTercerosAddNumeroreferidosAndNumeroordenesreferidos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terceros', function (Blueprint $table) {
            $table->bigInteger('numero_referidos')->nullable()->after('perfil');
            $table->integer('numero_ordenes_referidos')->nullable()->after('numero_referidos');
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
