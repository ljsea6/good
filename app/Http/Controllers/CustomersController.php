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
                'default_address' => (isset($customer['customer']['default_address'])) ? $event_json['default_address'] : null,
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
            
            $result = Tercero::where('email', $customer['email'])->get();
                    
                    if(count($result) === 0) {
                        $aux = explode('@', strtolower($customer['email']));
                        $tercero = new Tercero();
                        $tercero->nombres = (empty($customer['first_name']) || $customer['first_name'] == null || $customer['first_name'] == '') ? $customer['email'] : $customer['first_name'];
                        $tercero->apellidos = strtolower($customer['last_name']);
                        $tercero->email = strtolower($customer['email']);
                        $tercero->usuario = strtolower($customer['email']);
                        $tercero->contraseña = bcrypt($aux[0]);
                        $tercero->tipo_id = 1;
                        $tercero->customer_id = $customer['customer_id'];
                        $tercero->network_id = $customer['network_id'];
                        $tercero->save();
                    }
            
            return response()->json(['status' => 'The resource is created successfully'], 200);
        }
                        
    }
}
