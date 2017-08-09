<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;
use App\Product;
use App\Entities\Tercero;
use App\Customer;

class OrdersController extends Controller
{
  
    public function create()
    {
       $input = file_get_contents('php://input');
       $order = json_decode($input, true);
       
       if ($order['financial_status'] === 'paid') {
           
           $result = Order::where('order_id', (int)$order['id'])
                                    ->where('email', $order['email'])
                                    ->where('network_id', 1)
                                    ->get();
           
           if (count($result === 0)) {
               Order::create([
                                'billing_address' => $order['billing_address'],
                                'browser_ip' => $order['browser_ip'],
                                'buyer_accepts_marketing' => $order['buyer_accepts_marketing'],
                                'cancel_reason' => $order['cancel_reason'],
                                'cancelled_at' => Carbon::parse($order['cancelled_at']),
                                'cart_token' => $order['cart_token'],
                                'client_details' => $order['client_details'],
                                'closed_at' => Carbon::parse($order['closed_at']),
                                'currency' => $order['currency'],
                                'customer_id' => $order['customer']['id'],
                                'discount_codes' => $order['discount_codes'],
                                'email' => strtolower($order['email']),
                                'financial_status' => $order['financial_status'],
                                'fulfillments' => $order['fulfillments'],
                                'fulfillment_status' => $order['fulfillment_status'],
                                'tags' => $order['tags'],
                                'gateway' => $order['gateway'],
                                'landing_site' => $order['landing_site'],
                                'landing_site_ref' => $order['landing_site_ref'],
                                'line_items' => $order['line_items'],
                                'location_id' => $order['location_id'],
                                'name' => $order['name'],
                                'network_id' => 1,
                                'note' => $order['note'],
                                'note_attributes' => $order['note_attributes'],
                                'number' => $order['number'],
                                'order_id' => (int)$order['id'],
                                'order_number' => $order['order_number'],
                                'payment_details' => null,
                                'payment_gateway_names' => $order['payment_gateway_names'],
                                'phone' => $order['phone'],
                                'processed_at' => Carbon::parse($order['processed_at']),
                                'processing_method' => $order['processing_method'],
                                'referring_site' => $order['referring_site'],
                                'refunds' => $order['refunds'],
                                'shipping_address' => (!empty($order['shipping_address'])) ?$order['shipping_address'] : null,
                                'shipping_lines' => $order['shipping_lines'],
                                'source_name' => $order['source_name'],
                                'subtotal_price' => $order['subtotal_price'],
                                'tax_lines' => $order['tax_lines'],
                                'taxes_included' => $order['taxes_included'],
                                'token' => $order['token'],
                                'total_discounts' => $order['total_discounts'],
                                'total_line_items_price' => $order['total_line_items_price'],
                                'total_price' => $order['total_price'],
                                'total_tax' => $order['total_tax'],
                                'total_weight' => $order['total_weight'],
                                'user_id' => $order['user_id'],
                                'order_status_url' => $order['order_status_url'],
                                'created_at' => Carbon::parse($order['created_at']),
                                'updated_at' => Carbon::parse($order['updated_at']),
                                'test' => $order['test'],
                                'confirmed' => $order['confirmed'],
                                'total_price_usd' => $order['total_price_usd'],
                                'checkout_token' => $order['checkout_token'],
                                'reference' => $order['reference'],
                                'source_identifier' => $order['source_identifier'],
                                'source_url' => $order['source_url'],
                                'device_id' => $order['device_id'],
                                'checkout_id' => $order['checkout_id'],
                            ]);
                            
                            if ($order['line_items'][0]['product_id'] !== null) {
                                $product = Product::where('id', $order['line_items'][0]['product_id'])->get();

                                if (count($product) > 0) {
                                   $find = Product::find($product[0]['id']);
                                   $find->precio_unidad = $order['line_items'][0]['price'];
                                   $find->unidades_vendidas = $find->unidades_vendidas + 1;
                                   $find->save();
                                }
                            }

                            $result = Customer::where('email', strtolower($order['email']))
                                        ->where('customer_id', $order['customer']['id'])
                                        ->get();

                            if (count($result) > 0) {

                               $tercero = Tercero::where('email', strtolower($result[0]['last_name']))->get();

                                if (count($tercero) > 0) {
                                   $find = Tercero::find($tercero[0]['id']);
                                   $total = $find->total_price_orders + $order['total_price'];
                                   $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                   $find->total_price_orders = (double)$total;
                                   $find->save();
                                }

                                if (count($tercero) == 0) {
                                   $find = Tercero::find(5);
                                   $total = $find->total_price_orders + $order['total_price'];
                                   $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                   $find->total_price_orders = (double)$total;
                                   $find->save();
                                }
                            }
                            return response()->json(['status' => 'The resource is created successfully'], 200);
           }
                            
       }                      
    }
    
    public function update()
    {
       $input = file_get_contents('php://input');
       $order = json_decode($input, true);
       
       if ($order['financial_status'] === 'paid') {
                            
                            $result = Order::where('order_id', (int)$order['id'])
                                    ->where('email', $order['email'])
                                    ->where('network_id', 1)
                                    ->get();
                            
                            if (count($result) > 0) {
                                $update = Order::find($result[0]['id']);
                                $update->financial_status = $order['financial_status'];
                                $update->save();
                                
                                if ($order['line_items'][0]['product_id'] !== null) {
        
                                    $product = Product::where('id', $order['line_items'][0]['product_id'])->get();

                                    if (count($product) > 0) {
                                       $find = Product::find($product[0]['id']);
                                       $find->precio_unidad = $order['line_items'][0]['price'];
                                       $find->unidades_vendidas = $find->unidades_vendidas + 1;
                                       $find->save();
                                    }
                                }

                                $resultaux = Customer::where('email', strtolower($order['email']))
                                            ->where('customer_id', $order['customer']['id'])
                                            ->get();

                                if (count($resultaux) > 0) {

                                   $tercero = Tercero::where('email', strtolower($resultaux[0]['last_name']))->get();

                                    if (count($tercero) > 0) {
                                       $find = Tercero::find($tercero[0]['id']);
                                       $total = $find->total_price_orders + $order['total_price'];
                                       $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                       $find->total_price_orders = (double)$total;
                                       $find->save();
                                    }

                                    if (count($tercero) == 0) {
                                       $find = Tercero::find(5);
                                       $total = $find->total_price_orders + $order['total_price'];
                                       $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                       $find->total_price_orders = (double)$total;
                                       $find->save();
                                    }
                                }
                                return response()->json(['status' => 'The resource is created successfully'], 200);
                            }
                            
                            if (count($result) === 0) {
                                
                                Order::create([
                                    'billing_address' => $order['billing_address'],
                                    'browser_ip' => $order['browser_ip'],
                                    'buyer_accepts_marketing' => $order['buyer_accepts_marketing'],
                                    'cancel_reason' => $order['cancel_reason'],
                                    'cancelled_at' => Carbon::parse($order['cancelled_at']),
                                    'cart_token' => $order['cart_token'],
                                    'client_details' => $order['client_details'],
                                    'closed_at' => Carbon::parse($order['closed_at']),
                                    'currency' => $order['currency'],
                                    'customer_id' => $order['customer']['id'],
                                    'discount_codes' => $order['discount_codes'],
                                    'email' => strtolower($order['email']),
                                    'financial_status' => $order['financial_status'],
                                    'fulfillments' => $order['fulfillments'],
                                    'fulfillment_status' => $order['fulfillment_status'],
                                    'tags' => $order['tags'],
                                    'gateway' => $order['gateway'],
                                    'landing_site' => $order['landing_site'],
                                    'landing_site_ref' => $order['landing_site_ref'],
                                    'line_items' => $order['line_items'],
                                    'location_id' => $order['location_id'],
                                    'name' => $order['name'],
                                    'network_id' => 1,
                                    'note' => $order['note'],
                                    'note_attributes' => $order['note_attributes'],
                                    'number' => $order['number'],
                                    'order_id' => (int)$order['id'],
                                    'order_number' => $order['order_number'],
                                    'payment_details' => null,
                                    'payment_gateway_names' => $order['payment_gateway_names'],
                                    'phone' => $order['phone'],
                                    'processed_at' => Carbon::parse($order['processed_at']),
                                    'processing_method' => $order['processing_method'],
                                    'referring_site' => $order['referring_site'],
                                    'refunds' => $order['refunds'],
                                    'shipping_address' => (!empty($order['shipping_address'])) ?$order['shipping_address'] : null,
                                    'shipping_lines' => $order['shipping_lines'],
                                    'source_name' => $order['source_name'],
                                    'subtotal_price' => $order['subtotal_price'],
                                    'tax_lines' => $order['tax_lines'],
                                    'taxes_included' => $order['taxes_included'],
                                    'token' => $order['token'],
                                    'total_discounts' => $order['total_discounts'],
                                    'total_line_items_price' => $order['total_line_items_price'],
                                    'total_price' => $order['total_price'],
                                    'total_tax' => $order['total_tax'],
                                    'total_weight' => $order['total_weight'],
                                    'user_id' => $order['user_id'],
                                    'order_status_url' => $order['order_status_url'],
                                    'created_at' => Carbon::parse($order['created_at']),
                                    'updated_at' => Carbon::parse($order['updated_at']),
                                    'test' => $order['test'],
                                    'confirmed' => $order['confirmed'],
                                    'total_price_usd' => $order['total_price_usd'],
                                    'checkout_token' => $order['checkout_token'],
                                    'reference' => $order['reference'],
                                    'source_identifier' => $order['source_identifier'],
                                    'source_url' => $order['source_url'],
                                    'device_id' => $order['device_id'],
                                    'checkout_id' => $order['checkout_id'],
                                ]);
                                
                                if ($order['line_items'][0]['product_id'] !== null) {
                                    $product = Product::where('id', $order['line_items'][0]['product_id'])->get();

                                    if (count($product) > 0) {
                                       $find = Product::find($product[0]['id']);
                                       $find->precio_unidad = $order['line_items'][0]['price'];
                                       $find->unidades_vendidas = $find->unidades_vendidas + 1;
                                       $find->save();
                                    }
                                }

                                $resultaux = Customer::where('email', strtolower($order['email']))
                                            ->where('customer_id', $order['customer']['id'])
                                            ->get();

                                if (count($resultaux) > 0) {

                                    $tercero = Tercero::where('email', strtolower($resultaux[0]['last_name']))->get();

                                     if (count($tercero) > 0) {
                                        $find = Tercero::find($tercero[0]['id']);
                                        $total = $find->total_price_orders + $order['total_price'];
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                        $find->total_price_orders = (double)$total;
                                        $find->save();
                                     }

                                     if (count($tercero) == 0) {
                                        $find = Tercero::find(5);
                                        $total = $find->total_price_orders + $order['total_price'];
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                        $find->total_price_orders = (double)$total;
                                        $find->save();
                                     }
                                }
                                
                                return response()->json(['status' => 'The resource is created successfully'], 200);
                            }
       }  
    }
    
    public function delete()
    {
       $input = file_get_contents('php://input');
       $order = json_decode($input, true);
       if ($order['financial_status'] !== 'paid') {
           Order::where('order_id', (int)$order['id'])
                                    ->where('email', $order['email'])
                                    ->where('network_id', 1)
                                    ->delete();
           
                                if ($order['line_items'][0]['product_id'] !== null) {
                                    $product = Product::where('id', $order['line_items'][0]['product_id'])->get();

                                    if (count($product) > 0) {
                                       $find = Product::find($product[0]['id']);
                                       $find->unidades_vendidas = $find->unidades_vendidas - 1;
                                       $find->save();
                                    }
                                }

                                $resultaux = Customer::where('email', strtolower($order['email']))
                                            ->where('customer_id', $order['customer']['id'])
                                            ->get();

                                if (count($resultaux) > 0) {

                                    $tercero = Tercero::where('email', strtolower($resultaux[0]['last_name']))->get();

                                     if (count($tercero) > 0) {
                                        $find = Tercero::find($tercero[0]['id']);
                                        $total = $find->total_price_orders - $order['total_price'];
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos - 1;
                                        $find->total_price_orders = (double)$total;
                                        $find->save();
                                     }
                                     
                                    if (count($tercero) == 0) {
                                       $find = Tercero::find(5);
                                       $total = $find->total_price_orders - $order['total_price'];
                                       $find->numero_ordenes_referidos = $find->numero_ordenes_referidos - 1;
                                       $find->total_price_orders = (double)$total;
                                       $find->save();
                                    }
                                }
        
            return response()->json(['status' => 'The resource is created successfully'], 200);   
       }
      
    } 
}
