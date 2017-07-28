<?php

use Illuminate\Database\Seeder;

class ZonaTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        App\Entities\Zona::create([
            'nombre' => 'Test',
            'ciudad_id' => '1',
        ]);
    }
}
