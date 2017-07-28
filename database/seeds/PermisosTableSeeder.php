<?php

use Illuminate\Database\Seeder;

class PermisosTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Bican\Roles\Models\Permission::create([
            'name'        => 'Crear usuarios',
            'slug'        => 'crear.usuarios',
            'description' => 'Creacion de usuarios'
        ]);
        Bican\Roles\Models\Permission::create([
            'name'        => 'Editar usuarios',
            'slug'        => 'editar.usuarios',
            'description' => 'Edicion de usuarios'
        ]);
        Bican\Roles\Models\Permission::create([
            'name'        => 'Eliminar usuarios',
            'slug'        => 'eliminar.usuarios',
            'description' => 'Eliminacion de usuarios'
        ]);
    }
}
