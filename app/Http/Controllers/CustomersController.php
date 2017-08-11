<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 500);

use App\Customer;
use App\Order;
use App\Entities\Network;
use App\Entities\Tercero;
use App\Product;
use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class CustomersController extends Controller
{
   
    public function create(Request $request)
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
                        
                       $finder = Tercero::where('email', $event_json['last_name'])->where('state', true)->get();
                        
                        if (count($finder) > 0) {
                            DB::table('terceros_networks')->insert([
                                'customer_id' => $tercero->id,
                                'network_id' => 1,
                                'padre_id' => $finder[0]['id'],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                            
                            $count = Tercero::find($finder[0]['id']);
                            $count->numero_referidos = $count->numeroreferidos + 1;
                            $count->save();
                            
                        } else {
                            DB::table('terceros_networks')->insert([
                                'customer_id' => $tercero->id,
                                'network_id' => 1,
                                'padre_id' => 26,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                            $count = Tercero::find(26);
                            $count->numero_referidos = $count->numero_referidos + 1;
                            $count->save();
                        }
                    }
            
            return response()->json(['status' => 'The resource is created successfully'], 200);
        }
                        
    }
    
    public function meta () 
    {
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $result_url = explode('.', $api_url);
        
        $res = $client->request('POST', $api_url . '/admin/customers/6240624769/metafields.json', [
                "metafield" => [
                "namespace" => "customers",
                "key" => "probando",
                "value" => 25,
                "value_type" => "integer"
              ]
        ]);
        
        return json_decode($res->getBody(), true);
    }
    
}
