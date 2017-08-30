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
use DB;

class OrdersController extends Controller {

    function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, 'afc86df7e11dcbe0ab414fa158ac1767', true));
        return hash_equals($hmac_header, $calculated_hmac);
    }

    public function create()
    {
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

        $input = file_get_contents('php://input');
        $order = json_decode($input, true);

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($order), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {
            $result = Order::where('order_id', $order['id'])
                    ->where('email', $order['email'])
                    ->where('network_id', 1)
                    ->first();

            if (count($result) == 0) {
                $owner = Customer::where('email', $order['email'])->first();

                if (count($owner) > 0) {

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
                        'order_id' => (int) $order['id'],
                        'order_number' => $order['order_number'],
                        'payment_details' => null,
                        'payment_gateway_names' => $order['payment_gateway_names'],
                        'phone' => $order['phone'],
                        'processed_at' => Carbon::parse($order['processed_at']),
                        'processing_method' => $order['processing_method'],
                        'referring_site' => $order['referring_site'],
                        'refunds' => $order['refunds'],
                        'shipping_address' => (!empty($order['shipping_address'])) ? $order['shipping_address'] : null,
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
                        'origin' => 'webhooks'
                    ]);

                    if ($order['financial_status'] == "paid") {

                        $product = Product::where('id', $order['line_items'][0]['product_id'])->get();

                        if (count($product) > 0) {
                            $find = Product::find($product[0]['id']);
                            $find->precio_unidad = $order['line_items'][0]['price'];
                            $find->unidades_vendidas = $find->unidades_vendidas + 1;
                            $find->save();
                        }

                        $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                        if (count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                            $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                            if ($padre->state) {

                                $find = Tercero::find($padre->id);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                if (count($customer) > 0) {

                                    $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                    $metafields = json_decode($res->getBody(), true);
                                    $results = array();

                                    if (count($metafields['metafields']) > 0) {

                                        foreach ($metafields['metafields'] as $metafield) {

                                            if ($metafield['key'] === 'referidos') {
                                                $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace' => 'customers',
                                                                'key' => 'referidos',
                                                                'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                'value_type' => 'integer'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                            }

                                            if ($metafield['key'] === 'compras') {
                                                $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace' => 'customers',
                                                                'key' => 'compras',
                                                                'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                'value_type' => 'integer'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                            }

                                            if ($metafield['key'] === 'valor') {
                                                $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace' => 'customers',
                                                                'key' => 'valor',
                                                                'value' => '' . ( $find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                'value_type' => 'string'
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
                    }

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            } else {
                return response()->json(['status' => 'order not processed'], 200);
            }
        }
    }

    public function update()
    {
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

        $input = file_get_contents('php://input');
        $order = json_decode($input, true);

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($order), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {

            if ($order['financial_status'] == 'paid') {

                $result = Order::where('order_id', $order['id'])
                        ->where('email', $order['email'])
                        ->where('network_id', 1)
                        ->first();

                if (count($result) > 0) {

                    if ($result->financial_status != "paid") {

                        $update = Order::find($result->id);
                        $update->financial_status = $order['financial_status'];
                        $update->save();

                        $product = Product::where('id', $order['line_items'][0]['product_id'])->get();

                        if (count($product) > 0) {

                            $find = Product::find($product[0]['id']);
                            $find->precio_unidad = $order['line_items'][0]['price'];
                            $find->unidades_vendidas = $find->unidades_vendidas + 1;
                            $find->save();
                        }

                        $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                        if (count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                            $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                            if ($padre->state) {

                                $find = Tercero::find($padre->id);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                if (count($customer) > 0) {

                                    $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                    $metafields = json_decode($res->getBody(), true);
                                    $results = array();

                                    if (count($metafields['metafields']) > 0) {

                                        foreach ($metafields['metafields'] as $metafield) {

                                            if ($metafield['key'] === 'referidos') {
                                                $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace' => 'customers',
                                                                'key' => 'referidos',
                                                                'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                'value_type' => 'integer'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                            }

                                            if ($metafield['key'] === 'compras') {
                                                $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace' => 'customers',
                                                                'key' => 'compras',
                                                                'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                'value_type' => 'integer'
                                                            )
                                                        )
                                                    )
                                                );

                                                array_push($results, json_decode($res->getBody(), true));
                                            }

                                            if ($metafield['key'] === 'valor') {
                                                $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace' => 'customers',
                                                                'key' => 'valor',
                                                                'value' => '' . ( $find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                'value_type' => 'string'
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
                        return response()->json(['status' => 'order not processed'], 200);
                    }
                } else {

                    $owner = Customer::where('email', $order['email'])->first();
                    if (count($owner) > 0) {

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
                            'order_id' => (int) $order['id'],
                            'order_number' => $order['order_number'],
                            'payment_details' => null,
                            'payment_gateway_names' => $order['payment_gateway_names'],
                            'phone' => $order['phone'],
                            'processed_at' => Carbon::parse($order['processed_at']),
                            'processing_method' => $order['processing_method'],
                            'referring_site' => $order['referring_site'],
                            'refunds' => $order['refunds'],
                            'shipping_address' => (!empty($order['shipping_address'])) ? $order['shipping_address'] : null,
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
                            'origin' => 'webhooks'
                        ]);

                        if ($order['financial_status'] == "paid") {

                            $product = Product::where('id', $order['line_items'][0]['product_id'])->get();

                            if (count($product) > 0) {

                                $find = Product::find($product[0]['id']);
                                $find->precio_unidad = $order['line_items'][0]['price'];
                                $find->unidades_vendidas = $find->unidades_vendidas + 1;
                                $find->save();
                            }

                            $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                            if (count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if ($padre->state) {

                                    $find = Tercero::find($padre->id);
                                    $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                    $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                    $find->ganacias = $find->total_price_orders * 0.05;
                                    $find->save();

                                    $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                    if (count($customer) > 0) {

                                        $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                        $metafields = json_decode($res->getBody(), true);
                                        $results = array();

                                        if (count($metafields['metafields']) > 0) {

                                            foreach ($metafields['metafields'] as $metafield) {

                                                if ($metafield['key'] === 'referidos') {
                                                    $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                            'form_params' => array(
                                                                'metafield' => array(
                                                                    'namespace' => 'customers',
                                                                    'key' => 'referidos',
                                                                    'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                    'value_type' => 'integer'
                                                                )
                                                            )
                                                        )
                                                    );

                                                    array_push($results, json_decode($res->getBody(), true));
                                                }

                                                if ($metafield['key'] === 'compras') {
                                                    $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                            'form_params' => array(
                                                                'metafield' => array(
                                                                    'namespace' => 'customers',
                                                                    'key' => 'compras',
                                                                    'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                    'value_type' => 'integer'
                                                                )
                                                            )
                                                        )
                                                    );

                                                    array_push($results, json_decode($res->getBody(), true));
                                                }

                                                if ($metafield['key'] === 'valor') {
                                                    $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                            'form_params' => array(
                                                                'metafield' => array(
                                                                    'namespace' => 'customers',
                                                                    'key' => 'valor',
                                                                    'value' => '' . ( $find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                    'value_type' => 'string'
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
                        }

                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    }
                }
            } else {
                return response()->json(['status' => 'order not processed'], 200);
            }
        }
    }

    public function delete()
    {
        /*

        $input = file_get_contents('php://input');
        $order = json_decode($input, true);
        if ($order['financial_status'] !== 'paid') {
            Order::where('order_id', (int) $order['id'])
                    ->where('email', $order['email'])
                    ->where('network_id', 1)
                    ->delete();

            $product = Product::where('id', $order['line_items'][0]['product_id'])->get();

            if (count($product) > 0) {
                $find = Product::find($product[0]['id']);
                $find->unidades_vendidas = $find->unidades_vendidas - 1;
                $find->save();
            }

            $resultaux = Customer::where('email', strtolower($order['email']))
                    ->where('customer_id', $order['customer']['id'])
                    ->where('network_id', 1)
                    ->get();

            if (count($resultaux) > 0) {

                $tercero = Tercero::where('email', $resultaux[0]['last_name'])->where('state', true)->first();

                if (count($tercero) > 0) {
                    $find = Tercero::find($tercero->id);
                    DB::table('terceros')->where('id', $find->id)->update(['total_price_orders' => $find->total_price_orders - $order['total_price']]);
                    DB::table('terceros')->where('id', $find->id)->update(['numero_ordenes_referidos' => $find->numero_ordenes_referidos - 1]);
                }

                if (count($tercero) == 0) {
                    $find = Tercero::find(26);
                    DB::table('terceros')->where('id', 26)->update(['total_price_orders' => $find->total_price_orders - $order['total_price']]);
                    DB::table('terceros')->where('id', 26)->update(['numero_ordenes_referidos' => $find->numero_ordenes_referidos - 1]);
                }
            }

            return response()->json(['status' => 'The resource is created successfully'], 200);
        }
        *
         */
    }

}
