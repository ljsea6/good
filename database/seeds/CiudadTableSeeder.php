<?php

use Illuminate\Database\Seeder;

class CiudadTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        App\Entities\Ciudad::create([
            'codigo_dane'  => 123,
            'nombre'       => 'Medellin',
            'departamento' => 'Antioquia',
            'region'       => 'Norte',
            'tipo_id'       => '1',
        ]);
    }
}
