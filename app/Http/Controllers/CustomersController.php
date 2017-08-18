<?php

namespace App\Http\Controllers;



use App\Customer;
use App\Order;
use App\Entities\Network;
use App\Entities\Tercero;
use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use DB;


class CustomersController extends Controller
{
    
    
    
    public function create()
    {
        $input = file_get_contents('php://input');
        $event_json = json_decode($input, true);
         
        $result = Customer::where('customer_id', $event_json['id'])
                 ->where('email', strtolower($event_json['email']))
                 ->where('network_id', 1)
                 ->get();
         
        if (count($result) === 0) {
            
            Customer::create([
                'accepts_marketing' => $event_json['accepts_marketing'],
                'addresses' => $event_json['addresses'],
                'created_at' => Carbon::parse($event_json['created_at']),
                'default_address' => (isset($event_json['customer']['default_address'])) ? $event_json['default_address'] : null,
                'email' => strtolower($event_json['email']),
                'phone' => $event_json['phone'],
                'first_name' => $event_json['first_name'],
                'customer_id' => $event_json['id'],
                'metafield' => null,
                'multipass_identifier' => $event_json['multipass_identifier'],
                'last_name' => strtolower($event_json['last_name']),
                'last_order_id' => $event_json['last_order_id'],
                'last_order_name' => $event_json['last_order_name'],
                'network_id' => 1,
                'note' => $event_json['note'],
                'orders_count' => $event_json['orders_count'],
                'state' => $event_json['state'],
                'tags' => $event_json['tags'],
                'tax_exempt' => $event_json['tax_exempt'],
                'total_spent' => $event_json['total_spent'],
                'updated_at' => Carbon::parse($event_json['updated_at']),
                'verified_email' => $event_json['verified_email'],
            ]);
            
                    $result = Tercero::where('email', $event_json['email'])->get();
                    
                    if(count($result) === 0) {
                        $aux = explode('@', strtolower($event_json['email']));
                        $tercero = new Tercero();
                        $tercero->nombres = (empty($event_json['first_name']) || $event_json['first_name'] == null || $event_json['first_name'] == '') ? $event_json['email'] : $event_json['first_name'];
                        $tercero->apellidos = strtolower($event_json['last_name']);
                        $tercero->email = strtolower($event_json['email']);
                        $tercero->usuario = strtolower($event_json['email']);
                        $tercero->contraseña = bcrypt($aux[0]);
                        $tercero->tipo_id = 1;
                        $tercero->customer_id = $event_json['id'];
                        $tercero->network_id = 1;
                        $tercero->save();
                        
                        $finder = Tercero::where('email', $event_json['last_name'])->where('state', true)->first();
                        
                        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                        $client = new \GuzzleHttp\Client();
                        
                        if (count($finder) > 0) {
                            
                            $tercero->networks()->attach(1, ['padre_id' => $finder->id]);
                            
                            $father = Tercero::find($finder->id);
                            $father->numero_referidos = $father->numero_referidos + 1;
                            $father->save();
                            
                            /*
                            $findcustomer = Customer::where('customer_id', $father->customer_id)
                                            ->where('email', $father->email)
                                            ->first();
                            
                            if (count($findcustomer) > 0) {
                                $res = $client->request('get', $api_url . '/admin/customers/'. $father->customer_id .'/metafields.json');
                                $metafields = json_decode($res->getBody(), true);

                                $results = array();

                                if (count($metafields['metafields']) > 0) {

                                    foreach ($metafields['metafields'] as $metafield) {

                                        if ($metafield['key'] === 'referidos') {
                                                $res = $client->request('put', $api_url . '/admin/customers/'. $father->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace'=>'customers',                                                                                              
                                                                'key'=> 'referidos',
                                                                'value'=> ($father->numero_referidos == null || $father->numero_referidos == null) ? 0 : $father->numero_referidos,
                                                                'value_type'=>'integer'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                        }

                                        if ($metafield['key'] === 'compras') {
                                                $res = $client->request('put', $api_url . '/admin/customers/'. $father->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace'=>'customers',                                                                                              
                                                                'key'=> 'compras',
                                                                'value'=> ($father->numero_ordenes_referidos == null || $father->numero_ordenes_referidos == 0) ? 0 : $father->numero_ordenes_referidos,
                                                                'value_type'=>'integer'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                        }

                                        if ($metafield['key'] === 'valor') {
                                                $res = $client->request('put', $api_url . '/admin/customers/'. $father->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace'=>'customers',                                                                                              
                                                                'key'=> 'valor',
                                                                'value'=> '' . ($father->total_price_orders == null || $father->total_price_orders == 0) ? 0 : $father->total_price_orders . '',
                                                                'value_type'=>'string'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                        }

                                    }

                                } else {

                                    $res = $client->request('post', $api_url . '/admin/customers/'. $father->customer_id .'/metafields.json', array(
                                        'form_params' => array(
                                            'metafield' => array(
                                                'namespace'=>'customers',                                                                                              
                                                'key'=> 'referidos',
                                                'value'=> ($father->numero_referidos == null || $father->numero_referidos == 0) ?  0: $father->numero_referidos,
                                                'value_type'=>'integer'
                                            )
                                        )
                                    ));

                                    array_push($results, json_decode($res->getBody(), true));

                                    $res = $client->request('post', $api_url . '/admin/customers/'. $father->customer_id .'/metafields.json', array(
                                        'form_params' => array(
                                            'metafield' => array(
                                                'namespace'=>'customers',                                                                                              
                                                'key'=> 'compras',
                                                'value'=> ($father->numero_ordenes_referidos == null ||  $father->numero_ordenes_referidos == 0) ? 0 : $father->numero_ordenes_referidos,
                                                'value_type'=>'integer'
                                            )
                                        )
                                    ));

                                    array_push($results, json_decode($res->getBody(), true));

                                    $res = $client->request('post', $api_url . '/admin/customers/'. $father->customer_id .'/metafields.json', array(
                                        'form_params' => array(
                                            'metafield' => array(
                                                'namespace'=>'customers',                                                                                              
                                                'key'=> 'valor',
                                                'value'=> '' . ($father->total_price_orders == null || $father->total_price_orders == 0) ? 0 : $father->total_price_orders . '',
                                                'value_type'=>'string'
                                            )
                                        )
                                    ));

                                    array_push($results, json_decode($res->getBody(), true));

                                }

                            }
                            */
                                                        
                        } else {
                            
                            $tercero->networks()->attach(1, ['padre_id' => 26]);
                            
                            $father = Tercero::find(26);
                            $father->numero_referidos = $father->numero_referidos + 1;
                            $father->save();
                            
                            /*
                            $findcustomer = Customer::where('customer_id', $father->customer_id)
                                            ->where('email', $father->email)
                                            ->first();
                            
                            if (count($findcustomer) > 0) {
                                $res = $client->request('get', $api_url . '/admin/customers/'. $father->customer_id .'/metafields.json');
                                $metafields = json_decode($res->getBody(), true);

                                $results = array();

                                if (count($metafields['metafields']) > 0) {

                                    foreach ($metafields['metafields'] as $metafield) {

                                        if ($metafield['key'] === 'referidos') {
                                                $res = $client->request('put', $api_url . '/admin/customers/'. $father->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace'=>'customers',                                                                                              
                                                                'key'=> 'referidos',
                                                                'value'=> ($father->numero_referidos  == null || $father->numero_referidos == 0) ? 0 : $father->numero_referidos,
                                                                'value_type'=>'integer'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                        }

                                        if ($metafield['key'] === 'compras') {
                                                $res = $client->request('put', $api_url . '/admin/customers/'. $father->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace'=>'customers',                                                                                              
                                                                'key'=> 'compras',
                                                                'value'=> ($father->numero_ordenes_referidos == null || $father->numero_ordenes_referidos == 0) ? 0 : $father->numero_ordenes_referidos,
                                                                'value_type'=>'integer'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                        }

                                        if ($metafield['key'] === 'valor') {
                                                $res = $client->request('put', $api_url . '/admin/customers/'. $father->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace'=>'customers',                                                                                              
                                                                'key'=> 'valor',
                                                                'value'=> '' . ($father->total_price_orders == null || $father->total_price_orders == 0) ? 0 : $father->total_price_orders . '',
                                                                'value_type'=>'string'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                        }

                                    }

                                } else {

                                    $res = $client->request('post', $api_url . '/admin/customers/'. $father->customer_id .'/metafields.json', array(
                                        'form_params' => array(
                                            'metafield' => array(
                                                'namespace'=>'customers',                                                                                              
                                                'key'=> 'referidos',
                                                'value'=> ($father->numero_referidos  == null || $father->numero_referidos == 0) ? 0 : $father->numero_referidos,
                                                'value_type'=>'integer'
                                            )
                                        )
                                    ));

                                    array_push($results, json_decode($res->getBody(), true));

                                    $res = $client->request('post', $api_url . '/admin/customers/'. $father->customer_id .'/metafields.json', array(
                                        'form_params' => array(
                                            'metafield' => array(
                                                'namespace'=>'customers',                                                                                              
                                                'key'=> 'compras',
                                                'value'=> ($father->numero_ordenes_referidos == null || $father->numero_ordenes_referidos == 0) ? 0 : $father->numero_ordenes_referidos,
                                                'value_type'=>'integer'
                                            )
                                        )
                                    ));

                                    array_push($results, json_decode($res->getBody(), true));

                                    $res = $client->request('post', $api_url . '/admin/customers/'. $father->customer_id .'/metafields.json', array(
                                        'form_params' => array(
                                            'metafield' => array(
                                                'namespace'=>'customers',                                                                                              
                                                'key'=> 'valor',
                                                'value'=> '' . ($father->total_price_orders == null || $father->total_price_orders == 0) ? 0 : $father->total_price_orders . '',
                                                'value_type'=>'string'
                                            )
                                        )
                                    ));

                                    array_push($results, json_decode($res->getBody(), true));

                                }

                            }
                             * *
                             */
                        }
                        
                    }
            
            return response()->json(['status' => 'The resource is created successfully'], 200);
        }
                        
    }
   
    
    public function meta () 
    {
        
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        
        $terceros = Tercero::all();
        $results = array();
        
        
        foreach ($terceros as $tercero) {
           
            $ganacia = $tercero->total_price_orders * 0.05;
            
            if ($ganacia >= 1000) {
                
                $find = Customer::where('customer_id', $tercero->customer_id)
                    ->where('email', $tercero->email)
                    ->first();
                
                if (count($find) > 0) {

                    $res = $client->request('get', $api_url . '/admin/customers/'. $tercero->customer_id .'/metafields.json', ['delay' => 1, 'timeout' =>  1]);
                    $metafields = json_decode($res->getBody(), true);
                    
                    /*
                    if (isset($metafields['metafields']) && count($metafields['metafields']) > 0) {
                        
                       foreach ($metafields['metafields'] as $metafield) {

                               $res = $client->request('delete', $api_url . '/admin/metafields/'. $metafield['id'] .'.json');
                                array_push($results, json_decode($res->getBody(), true));
                        }
                    } 
                     * 
                     */
                    
 
                      
                    if (isset($metafields['metafields']) && count($metafields['metafields']) == 0) {
                        
                        $resd = $client->request('post', $api_url . '/admin/customers/'. $tercero->customer_id .'/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace'=>'customers',                                                                                              
                                    'key'=> 'referidos',
                                    'value'=> ($tercero->numero_referidos == null) ? 0 : $tercero->numero_referidos,
                                    'value_type'=>'integer'
                                )
                            )
                        ));

                        array_push($results, json_decode($resd->getBody(), true));

                        $rese = $client->request('post', $api_url . '/admin/customers/'. $tercero->customer_id .'/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace'=>'customers',                                                                                              
                                    'key'=> 'compras',
                                    'value'=> ($tercero->numero_ordenes_referidos == null) ? 0 : $tercero->numero_ordenes_referidos,
                                    'value_type'=>'integer'
                                )
                            )
                        ));


                        array_push($results, json_decode($rese->getBody(), true));

                        $resf = $client->request('post', $api_url . '/admin/customers/'. $tercero->customer_id .'/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace'=>'customers',                                                                                              
                                    'key'=> 'valor',
                                    'value'=> '' . ($tercero->total_price_orders == null ) ? 0 : $tercero->total_price_orders * 0.05 . '',
                                    'value_type'=>'string'
                                )
                            )
                        ));


                        array_push($results, json_decode($resf->getBody(), true));

                    }
                        
                }
               
            }
        
        }
        
        return $results;
        
        
    }
    
}
