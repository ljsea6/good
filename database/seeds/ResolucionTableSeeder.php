<?php

use Illuminate\Database\Seeder;

class ResolucionTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        App\Entities\Resolucion::create([
            'id' => '1',
            'numero' => '1',
            'prefijo' => 'P-',
            'digitos' => '5',
            'vencimiento' => 'now()',
            'desde' => '0001',
            'hasta' => '1000',
            'nombre' => 'Resolucion Nacional',
        ]);
    }
}
