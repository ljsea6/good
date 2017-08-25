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
use MP;


class CustomersController extends Controller
{
    
    function verify_webhook($data, $hmac_header)
    {
          $calculated_hmac = base64_encode(hash_hmac('sha256', $data, 'afc86df7e11dcbe0ab414fa158ac1767', true));
          return hash_equals($hmac_header, $calculated_hmac);
    }
    
    
    public function create()
    {
        $input = file_get_contents('php://input');
        $event_json = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($event_json), $hmac_header);
        $resultapi = error_log('Webhook verified: '.var_export($verified, true));
        
        if ($resultapi == 'true') {
            
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
                                                                    'value'=> '' . ($father->total_price_orders == null || $father->total_price_orders == 0) ? 0 : number_format($father->total_price_orders * 0.05) . '',
                                                                    'value_type'=>'string'
                                                                )
                                                            )
                                                        )
                                                    );

                                                    array_push($results, json_decode($res->getBody(), true));
                                            }

                                        }
                                    }
                                }

                            } else {

                                $tercero->networks()->attach(1, ['padre_id' => 26]);

                                $father = Tercero::find(26);
                                $father->numero_referidos = $father->numero_referidos + 1;
                                $father->save();

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
                                                                    'value'=> '' . ($father->total_price_orders == null || $father->total_price_orders == 0) ? 0 : number_format($father->total_price_orders * 0.05) . '',
                                                                    'value_type'=>'string'
                                                                )
                                                            )
                                                        )
                                                    );

                                                    array_push($results, json_decode($res->getBody(), true));
                                            }

                                        }
                                    }


                                }

                            }

                        }

                return response()->json(['status' => 'The resource is created successfully'], 200);
            } else {
                 return response()->json(['status' => 'The resource was not created successfully'], 200);
            }
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
                    
                    $update = Tercero::find($tercero->id);
                    $update->ganacias = $update->total_price_orders * 0.05;
                    $update->save();
                    
                    $res = $client->request('get', $api_url . '/admin/customers/'. $update->customer_id .'/metafields.json', ['delay' => 1, 'timeout' =>  1]);
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
                        
                        $resd = $client->request('post', $api_url . '/admin/customers/'. $update->customer_id .'/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace'=>'customers',                                                                                              
                                    'key'=> 'referidos',
                                    'value'=> ($update->numero_referidos == null) ? 0 : $update->numero_referidos,
                                    'value_type'=>'integer'
                                )
                            )
                        ));

                        array_push($results, json_decode($resd->getBody(), true));

                        $rese = $client->request('post', $api_url . '/admin/customers/'. $update->customer_id .'/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace'=>'customers',                                                                                              
                                    'key'=> 'compras',
                                    'value'=> ($update->numero_ordenes_referidos == null) ? 0 : $update->numero_ordenes_referidos,
                                    'value_type'=>'integer'
                                )
                            )
                        ));

                        array_push($results, json_decode($rese->getBody(), true));

                        $resf = $client->request('post', $api_url . '/admin/customers/'. $update->customer_id .'/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace'=>'customers',                                                                                              
                                    'key'=> 'valor',
                                    'value'=> '' . ($update->ganacias == null ) ? 0 : number_format($update->ganacias) . '',
                                    'value_type'=>'string'
                                )
                            )
                        ));

                        array_push($results, json_decode($resf->getBody(), true));
                        
                        $resf = $client->request('post', $api_url . '/admin/customers/'. $tercero->customer_id .'/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace'=>'customers',                                                                                              
                                    'key'=> 'redimir',
                                    'value'=> '' . ($update->redimido == null ) ? 0 : number_format($update->redimido) . '',
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
    
    public function gifts() 
    {
        
        /*
        $data = Carbon::today();
        $referir = array();
        $today = [
            'day' => (int)$data->day,
            'month' => (int)$data->month,
            'year' => (int)$data->year
        ];
        
        $terceros = Tercero::where('total_price_orders', '>', 0)->get();
        
        foreach ($terceros as $tercero) {
            
            //if (($tercero->total_price_orders * 0.05) >= 1000 ) {
               
                $referidos = DB::table('terceros_networks')->select('customer_id')->where('padre_id', $tercero->id)->get();

                if (count($referidos) > 0) {

                    foreach ($referidos as $referido) {

                        $search = Tercero::find($referido->customer_id);

                        $orders = Order::where('financial_status', 'paid')
                                ->where('redimir', false)
                                ->where('email', $search->email)
                                ->get();

                        if (count($orders) > 0 ) {
                            
                            foreach ($orders as $order) {
                                
                                if (($today['year'] == $order->created_at->year)  && ($today['month'] == $order->created_at->month)) {
                                    //$change = Order::find($order->id);
                                    //$change->redimir = true;
                                    //$change->save();
                                }
                            }
                        }
                    }
                }
            //} 
            $find = Tercero::find($tercero->id);
           
           
            $findcustomer = Customer::where('customer_id', $find->customer_id)
                                                            ->where('email', $find->email)
                                                            ->first();
            
             $data = [
                "gift_card" => [
                    "note" => "This is a note",
                    "initial_value" => $find->total_price_orders * 0.05,
                    "template_suffix" =>  "gift_cards.birthday.liquid",
                    "customer_id" => $find->customer_id
                ]
  
            ];
            array_push($referir, $data); 
            //$find->total_price_orders = 0;
            //$find->save(); 
                            
                                            if (count($findcustomer) > 0) {

                                                $res = $client->request('get', $api_url . '/admin/customers/'. $find->customer_id .'/metafields.json');
                                                $metafields = json_decode($res->getBody(), true);
                                                $results = array();

                                                if (count($metafields['metafields']) > 0) {

                                                    foreach ($metafields['metafields'] as $metafield) {

                                                        if ($metafield['key'] === 'referidos') {
                                                                $res = $client->request('put', $api_url . '/admin/customers/'. $find->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                                        'form_params' => array(
                                                                            'metafield' => array(
                                                                                'namespace'=>'customers',                                                                                              
                                                                                'key'=> 'referidos',
                                                                                'value'=> ($find->numero_referidos  == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                                'value_type'=>'integer'
                                                                            )
                                                                        )
                                                                    )
                                                                );

                                                                array_push($results, json_decode($res->getBody(), true));
                                                        }

                                                        if ($metafield['key'] === 'compras') {
                                                                $res = $client->request('put', $api_url . '/admin/customers/'. $find->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                                        'form_params' => array(
                                                                            'metafield' => array(
                                                                                'namespace'=>'customers',                                                                                              
                                                                                'key'=> 'compras',
                                                                                'value'=> ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                                'value_type'=>'integer'
                                                                            )
                                                                        )
                                                                    )
                                                                );

                                                                array_push($results, json_decode($res->getBody(), true));
                                                        }

                                                        if ($metafield['key'] === 'valor') {
                                                                $res = $client->request('put', $api_url . '/admin/customers/'. $find->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                                        'form_params' => array(
                                                                            'metafield' => array(
                                                                                'namespace'=>'customers',                                                                                              
                                                                                'key'=> 'valor',
                                                                                'value'=> '' . ($find->total_price_orders == null || $find->total_price_orders == 0) ? 0 : number_format($find->total_price_orders * 0.05) . '',
                                                                                'value_type'=>'string'
                                                                            )
                                                                        )
                                                                    )
                                                                );

                                                                array_push($results, json_decode($res->getBody(), true));
                                                        }

                                                    }
                                                }
                                            }
                            
        }   
         * 
         */
        
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        
        $tercero = Tercero::where('email', 'ange.manjarrez.lopez@gmail.com')->first();
       
        
        
        if ($tercero->ganacias >= 1000) {
            
            $valor_redimir = 0;
            $sons = DB::table('terceros_networks')->select('customer_id')->where('padre_id', $tercero->id)->get();
            
            foreach ($sons as $son) {
                $searchemail = Tercero::find($son->customer_id);
                $orders = Order::where('email', $searchemail->email)
                        ->where('financial_status', 'paid')
                        ->where('redimir', false)
                        ->get();

                $redimir = 0;
                
                foreach ($orders as $order) {
                    $redimir = $redimir + $order->total_price;
                    $findorder = Order::find($order->id);
                    $findorder->redimir = true;
                    $findorder->save();
                }
                
                $valor_redimir = $redimir * 0.05;
                $redimir = 0;
            }
            
            $tercero_update = Tercero::find($tercero->id);
            $tercero_update->redimido = $tercero_update->redimido + $valor_redimir;
            $tercero_update->save();
           
            $send =  [ 
                        'form_params' => [
                            'gift_card' => [
                                "note" => "This is a note",
                                "initial_value" => $valor_redimir,
                                "template_suffix" => "gift_cards.birthday.liquid",
                                "currency" => "COP",
                                "customer_id" => $tercero->customer_id,
                            ]
                        ]
                    ];
            
            $valor_redimir = 0;
            
            $res = $client->request('post', $api_url . '/admin/gift_cards.json', $send);
            
            $result = json_decode($res->getBody(), true);
           
            if (count($result['gift_card']) > 0) {
                
                $resa = $client->request('get', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields.json');
                                                $metafields = json_decode($resa->getBody(), true);
                                                $results = array();

                                                if (count($metafields['metafields']) > 0) {

                                                    foreach ($metafields['metafields'] as $metafield) {

                                                        if ($metafield['key'] === 'referidos') {
                                                                $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                                        'form_params' => array(
                                                                            'metafield' => array(
                                                                                'namespace'=>'customers',                                                                                              
                                                                                'key'=> 'referidos',
                                                                                'value'=> ($tercero_update->numero_referidos  == null || $tercero_update->numero_referidos == 0) ? 0 : $tercero_update->numero_referidos,
                                                                                'value_type'=>'integer'
                                                                            )
                                                                        )
                                                                    )
                                                                );

                                                                array_push($results, json_decode($resb->getBody(), true));
                                                        }

                                                        if ($metafield['key'] === 'compras') {
                                                                $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                                        'form_params' => array(
                                                                            'metafield' => array(
                                                                                'namespace'=>'customers',                                                                                              
                                                                                'key'=> 'compras',
                                                                                'value'=> ($tercero_update->numero_ordenes_referidos == null || $tercero_update->numero_ordenes_referidos == 0) ? 0 : $tercero_update->numero_ordenes_referidos,
                                                                                'value_type'=>'integer'
                                                                            )
                                                                        )
                                                                    )
                                                                );

                                                                array_push($results, json_decode($resb->getBody(), true));
                                                        }

                                                        if ($metafield['key'] === 'valor') {
                                                                $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                                        'form_params' => array(
                                                                            'metafield' => array(
                                                                                'namespace'=>'customers',                                                                                              
                                                                                'key'=> 'valor',
                                                                                'value'=> '' . ($tercero_update->ganacias == null || $tercero_update->ganacias == 0) ? 0 : number_format($tercero_update->ganacias) . '',
                                                                                'value_type'=>'string'
                                                                            )
                                                                        )
                                                                    )
                                                                );

                                                                array_push($results, json_decode($resb->getBody(), true));
                                                        }
                                                        
                                                        if ($metafield['key'] === 'redimir') {
                                                                $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/'. $metafield['id'] .'.json', array(
                                                                        'form_params' => array(
                                                                            'metafield' => array(
                                                                                'namespace'=>'customers',                                                                                              
                                                                                'key'=> 'redimir',
                                                                                'value'=> '' . ($tercero_update->redimido == null || $tercero_update->redimido == 0) ? 0 : number_format($tercero_update->redimido) . '',
                                                                                'value_type'=>'string'
                                                                            )
                                                                        )
                                                                    )
                                                                );

                                                                array_push($results, json_decode($resb->getBody(), true));
                                                        }

                                                    }
                 
                                                }
            
                return response()->json(['status' => $result], 200);
            }
                                                         
        }
    }
    
    public function mercado()
    {
        define('CLIENT_ID', "7134341661319721");
        define('CLIENT_SECRET', "b7cQUIoU5JF4iWVvjM0w1YeX4b7VwLpw");
        
        $mp = new MP (CLIENT_ID, CLIENT_SECRET);
        
        define('ACCESS_TOKEN', $mp->get_access_token());
        define('checkout', '/collections/notifications/');
        
        
        
        $orders = Order::where('financial_status', 'pending')->get();
        
        foreach ($orders as $order) {
            $result = $mp->get("/mercadopago_account/movements/search?access_token=" . ACCESS_TOKEN . '&reference_id=' . $order->checkout_id );
            if (count($result['response']['results']) > 0) {
                return $result['response']['results'];
            } 
        }
    }
}
