<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Network;
use App\Entities\Tercero;
use DB;
use App\Order;

class GetFathers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:fathers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para obtener todos los padres y sus referidos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $networks = Network::select('id', 'name')->get();

        foreach ($networks as $network) {

                if ($network['name'] == 'shopify') {

                    $terceros = Tercero::where('network_id', $network['id'])
                                        ->where('state', true)
                                        ->orderby('id')
                                        ->get();

                    if (count($terceros) > 0) {

                        foreach ($terceros as $tercero) {

                            $result = DB::table('terceros_networks')
                                ->where('network_id', $network['id'])
                                ->where('customer_id', $tercero['id'])
                                ->select('*')
                                ->get();

                            if(count($result) == 0){

                                $finder = Tercero::find($tercero['id']);

                                 if (empty($finder['apellidos']) || $finder['apellidos'] === null || $finder['apellidos'] === '') {

                                    $finder->networks()->attach($network['id'], ['padre_id' => 26]);
                                 }

                                if (!empty($finder['apellidos'])) {

                                    $father = Tercero::where('email', $finder['apellidos'])->select('id')->get();

                                    if (count($father) > 0) {
                                        $finder->networks()->attach($network['id'], ['padre_id' => $father[0]['id']]);
                                    } 
                                    
                                    if (count($father) == 0 ) {
                                        if ($finder['id'] <= 52) {
                                            $finder->networks()->attach($network['id'], ['padre_id' => null]);
                                        }
                                        
                                        if ($finder['id'] > 52) {
                                            $finder->networks()->attach($network['id'], ['padre_id' => 26]);
                                        }
                                    }
                                    
                                    
                                }     
                            }

                        }
                    }

                }

                if($network['name'] == 'to go') {

                    $terceros =  Tercero::where('network_id', $network['id'])->get();

                    if(count($terceros) > 0){
                        return 'to go tiene.';
                    }
                }
                
                
                
                $tercerosaux = Tercero::select('id', 'identificacion', 'nombres', 'apellidos', 'email')
                    ->where('state', true)
                    ->orderBy('id')
                    ->get();
               
                foreach ($tercerosaux as $tercero) {
                   
                    $find = Tercero::find($tercero->id);
                   
                    $i = 0;            
                    
                    $sons = DB::table('terceros_networks')
                               ->select('customer_id')
                               ->where('network_id', 1)
                               ->where('padre_id', $find->id)
                               ->get();
                    
                    $find->numero_referidos = count($sons);
                    
                    foreach ($sons as $son) {

                           $finder = Tercero::find($son->customer_id);

                           if ($finder->state) {

                                $result = Order::where('customer_id', $finder->customer_id)->where('network_id', 1)->get();

                                if (count($result) > 0) {
                                    $i = $i + count($result);
                                }
                           }

                    }
                    
                    
                    $find->numero_ordenes_referidos = $i;
                    $find->save();
                }
        }
        
        $this->info('Los padres y sus referidos han sido descargados correctamente');
    
    }
}
