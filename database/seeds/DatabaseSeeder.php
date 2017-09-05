<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();

        /*$this->call(TipoTableSeeder::class);
        $this->call(CiudadTableSeeder::class);
        $this->call(CargoTableSeeder::class);
        $this->call(ResolucionTableSeeder::class);
        $this->call(SucursalTableSeeder::class);
        $this->call(ZonaTableSeeder::class);
        $this->call(SectorTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(EstadoTableSeeder::class);*/
        factory(\App\User::class, 50)->create();
        Model::reguard();
    }
}
