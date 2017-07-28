<?php
use Illuminate\Database\Seeder;

class TipoTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        App\Entities\Tipo::create([
            'id' => '1',
            'nombre' => 'Terceros',
        ]);
        App\Entities\Tipo::create([
            'id' => '2',
            'nombre' => 'Usuario',
            'padre_id'  => '1',
        ]);
        App\Entities\Tipo::create([
            'id' => '3',
            'nombre' => 'Cliente',
            'padre_id'  => '1',
        ]);
        App\Entities\Tipo::create([
            'id' => '4',
            'nombre' => 'Mensajero',
            'padre_id'  => '1',
        ]);
        App\Entities\Tipo::create([
            'id' => '5',
            'nombre' => 'Courier',
            'padre_id'  => '1',
        ]);
        App\Entities\Tipo::create([
            'id' => '6',
            'nombre' => 'Tipo Mensajeria',
        ]);
        App\Entities\Tipo::create([
            'id' => '7',
            'nombre' => 'Masivo',
            'padre_id'  => '6',
        ]);
        App\Entities\Tipo::create([
            'id' => '8',
            'nombre' => 'Carga',
            'padre_id'  => '6',
        ]);
        App\Entities\Tipo::create([
            'id' => '9',
            'nombre' => 'Internacional',
            'padre_id'  => '6',
        ]);
        App\Entities\Tipo::create([
            'id' => '10',
            'nombre' => 'Tipo Tiempo Entrega',
        ]);
        App\Entities\Tipo::create([
            'id' => '11',
            'nombre' => '8 Horas (Urgente)',
            'padre_id'  => '10',
        ]);
        App\Entities\Tipo::create([
            'id' => '12',
            'nombre' => '24 Horas',
            'padre_id'  => '10',
        ]);
        App\Entities\Tipo::create([
            'id' => '13',
            'nombre' => 'Normal',
            'padre_id'  => '10',
        ]);      
        App\Entities\Tipo::create([
            'id' => '14',
            'nombre' => 'Tipo Destino',
        ]);
        App\Entities\Tipo::create([
            'id' => '15',
            'nombre' => 'Tipo A (Misma ciudad)',
            'padre_id'  => '14',
        ]);
        App\Entities\Tipo::create([
            'id' => '16',
            'nombre' => 'Tipo B (Ciudades principales)',
            'padre_id'  => '14',
        ]);
        App\Entities\Tipo::create([
            'id' => '17',
            'nombre' => 'Tipo C (Oriente)',
            'padre_id'  => '14',
        ]);
        App\Entities\Tipo::create([
            'id' => '18',
            'nombre' => 'Tipo D (Poblaciones Lejanas)',
            'padre_id'  => '14',
        ]);      
        App\Entities\Tipo::create([
            'id' => '19',
            'nombre' => 'Tipo Pago',
        ]);  
        App\Entities\Tipo::create([
            'id' => '20',
            'nombre' => 'Efectivo',
            'padre_id'  => '19',
        ]);  
        App\Entities\Tipo::create([
            'id' => '21',
            'nombre' => 'Credito',
            'padre_id'  => '19',
        ]);
        App\Entities\Tipo::create([
            'id' => '22',
            'nombre' => 'Cheque',
            'padre_id'  => '19',
        ]);
        App\Entities\Tipo::create([
            'id' => '23',
            'nombre' => 'Tipo Producto',
        ]);  
        App\Entities\Tipo::create([
            'id' => '24',
            'nombre' => 'Sobres',
            'padre_id'  => '23',
        ]);
        App\Entities\Tipo::create([
            'id' => '25',
            'nombre' => 'Cajas',
            'padre_id'  => '23',
        ]);
    }
}
