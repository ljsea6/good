<?php
use Illuminate\Database\Seeder;

class CargoTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		App\Entities\Cargo::create(['nombre' => 'test']);
	}
}
