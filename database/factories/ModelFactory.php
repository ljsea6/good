<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
 */

/*$factory->define(App\Entities\Tercero::class, function (Faker\Generator $faker) {
    return [
        'identificacion' => '123456789',
        'nombres'        => $faker->firstName,
        'apellidos'      => $faker->lastName,
        'direccion'      => $faker->address,
        'telefono'       => $faker->phoneNumber,
        'email'          => $faker->unique()->email,
        'usuario'        => $faker->unique()->userName,
        'contraseña'     => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
        'tipo_id'        => $faker->randomElement([1, 2, 3, 4]),
        'ciudad_id'      => 1,
        'cargo_id'       => 1,
        'sucursal_id'    => 1,
        'resolucion_id'  => 1,
        'zona_id'        => 1,
        'sector_id'      => 1,
    ];
});*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt('secret'),
        'remember_token' => str_random(10)
    ];
});
