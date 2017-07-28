<?php

use Illuminate\Database\Seeder;

class SucursalTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        App\Entities\Sucursal::create([
            'nombre' => 'Principal Medellin',
            'direccion' => 'Cra 22 No 20 - 20.',
            'telefono' => '31322322',
            'email' => 'email@domina.com',
            'ciudad_id' => '1',
            'resolucion_credito_id' => '1',
            'resolucion_contado_id' => '1',
        ]);
    }
}
