<?php

use Illuminate\Database\Seeder;

class SectorTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        App\Entities\Sector::create([
            'nombre' => 'Test',
        ]);
    }
}
