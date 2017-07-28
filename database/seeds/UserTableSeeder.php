<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        App\Entities\Tercero::create([
            'identificacion' => '1023010450',
            'nombres'        => 'Admin',
            'apellidos'      => 'Admin',
            'direccion'      => 'Direccion',
            'telefono'       => '123456789',
            'email'          => 'admin@admin.com',
            'usuario'        => 'admin',
            'contraseña'     => bcrypt('admin'),
            'remember_token' => str_random(10),
            'tipo_id'        => 1,
            'ciudad_id'      => 1,
            'cargo_id'       => 1,
            'sucursal_id'    => 1,
            'resolucion_id'  => 1,
            'zona_id'        => 1,
            'sector_id'      => 1,
        ]);

        factory(App\Entities\Tercero::class, 3000)->create();
    }
}
