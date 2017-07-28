<?php

use Illuminate\Database\Seeder;

class EstadoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        App\Entities\Estado::create([
            'nombre' => 'En Proceso',
            'modulo' => 'envios'  
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Entrega',
            'modulo' => 'envios'  
        ]);
		App\Entities\Estado::create([
            'nombre' => 'Devolucion',
            'modulo' => 'envios'  
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Retencion',
            'modulo' => 'envios'  
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Entregado bajo puerta',
            'modulo' => 'envios',
            'padre_id' => '2',
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Entregado/Digitalizado',
            'modulo' => 'envios',
            'padre_id' => '2',
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Devolucion Inicial',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]);        
        App\Entities\Estado::create([
            'nombre' => 'Direccion No Existe',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Destinatario Desconocido',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Cerrado o Desocupado',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Rehusa a recibir',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Dificil Acceso',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Cambio domilicio',
            'modulo' => 'envios', 
            'padre_id' => '2',
        ]);
        App\Entities\Estado::create([
            'nombre' => 'Rehusa a recibir',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]); 
        App\Entities\Estado::create([
            'nombre' => 'Fallecido',
            'modulo' => 'envios', 
            'padre_id' => '3',
        ]); 
        App\Entities\Estado::create([
            'nombre' => 'Robo',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]); 
        App\Entities\Estado::create([
            'nombre' => 'Zona Roja',
            'modulo' => 'envios',
            'padre_id' => '3',
        ]); 

        App\Entities\Estado::create([
            'nombre' => 'Creada',
            'modulo' => 'ordenes'  
        ]); 
        App\Entities\Estado::create([
            'nombre' => 'Cargada',
            'modulo' => 'ordenes'  
        ]); 
        App\Entities\Estado::create([
            'nombre' => 'Impresion',
            'modulo' => 'ordenes'  
        ]); 
        App\Entities\Estado::create([
            'nombre' => 'Alistamiento',
            'modulo' => 'ordenes'  
        ]); 
        App\Entities\Estado::create([
            'nombre' => 'En zona',
            'modulo' => 'ordenes'  
        ]); 
        
        App\Entities\Estado::create([
            'nombre' => 'Terminada',
            'modulo' => 'ordenes'  
        ]); 

        App\Entities\Estado::create([
            'nombre' => 'Cerrada',
            'modulo' => 'ordenes'  
        ]); 

    }
}
