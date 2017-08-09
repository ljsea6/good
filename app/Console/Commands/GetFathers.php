<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Network;
use App\Entities\Tercero;
use DB;

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
                
                $referidosaux = array();
                
                $tercerosaux = Tercero::select('id', 'identificacion', 'nombres', 'apellidos', 'email')
                    ->where('state', true)
                    ->orderBy('id')
                    ->get();
               
                foreach ($tercerosaux as $tercero) {
                    $sons = DB::table('terceros_networks')
                            ->where('terceros_networks.padre_id', '=', $tercero['id'])
                            ->get();
                    $find = Tercero::find($tercero['id']); 
                    $find->numero_referidos = count($sons);
                   
                    $i = 0;
                    foreach ($sons as $son)
                    {
                        $result = Tercero::find($son->customer_id);
                        
                        if ($result->state) {
                            $resultOrders = DB::table('orders')
                                    ->where('orders.customer_id', $result->customer_id)
                                    ->where('orders.email', $result->email)
                                    ->get();
                            $i = $i + count($resultOrders);
                        }
                        
                    }
                    $find->numero_ordenes_referidos = $i;
                    $find->save();
                }
        }
        
        $this->info('Los padres y sus referidos han sido descargados correctamente');
    
    }
}
