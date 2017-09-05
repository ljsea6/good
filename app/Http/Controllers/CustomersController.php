<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Order;
use App\Entities\Tercero;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use MP;
use App\Commision;
use App\Logorder;

class CustomersController extends Controller {

    public function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, 'afc86df7e11dcbe0ab414fa158ac1767', true));
        return hash_equals($hmac_header, $calculated_hmac);
    }

    public function create()
    {
        ini_set('memory_limit','300M');

        $input = file_get_contents('php://input');
        $event_json = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($event_json), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

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

                $result = Tercero::where('email', strtolower($event_json['email']))->get();

                if (count($result) === 0) {

                    /*if ($event_json['email'] == 'soportesoyhello@gmail.com') {

                        $aux = explode('@', strtolower($event_json['email']));
                        $tercero = new Tercero();
                        $tercero->nombres = (empty($event_json['first_name']) || $event_json['first_name'] == null || $event_json['first_name'] == '') ? $event_json['email'] : $event_json['first_name'];
                        $tercero->apellidos = strtolower($event_json['last_name']);
                        $tercero->email = strtolower('soyhello');
                        $tercero->usuario = strtolower($event_json['email']);
                        $tercero->contraseña = bcrypt($aux[0]);
                        $tercero->tipo_id = 1;
                        $tercero->customer_id = $event_json['id'];
                        $tercero->network_id = 1;
                        $tercero->save();

                        $tercero->networks()->attach(1, ['padre_id' => null]);

                        $hijos = Tercero::where('apellidos', $tercero->email)->get();

                        foreach ($hijos as $hijo) {

                            DB::table('terceros_networks')->where('customer_id', $hijo->id)->update(['padre_id' => $tercero->id]);
                            $update = Tercero::find(26);
                            $update->numero_referidos = $update->numero_referidos - 1;
                            $update->save();

                            $up = Tercero::find($tercero->id);
                            $up->numero_referidos = $up->numero_referidos + 1;
                            $up->save();

                            $orders = Order::where('email', $hijo->email)->get();

                            if (count($orders) > 0) {
                                foreach ($orders as $order) {
                                    $update = Tercero::find(26);
                                    $update->numero_ordenes_referidos = $update->numero_ordenes_referidos - 1;
                                    $update->total_price_orders = $update->total_price_orders - $order->total_price;
                                    $update->save();

                                    $up = Tercero::find($tercero->id);
                                    $up->numero_ordenes_referidos = $up->numero_ordenes_referidos + 1;
                                    $up->total_price_orders = $up->total_price_orders + $order->total_price;
                                    $up->save();
                                }
                            }
                        }

                        $update = Tercero::find(26);
                        $update->ganacias = $update->total_price_orders * 0.05;
                        $update->save();

                        $up = Tercero::find($tercero->id);
                        $up->ganacias = $up->total_price_orders * 0.05;
                        $up->save();

                    }*/

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

                    $finder = Tercero::where('email', strtolower($event_json['last_name']))->where('state', true)->first();

                    $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                    $client = new \GuzzleHttp\Client();
                    $results = array();

                    if (count($finder) > 0) {

                            $tercero->networks()->attach(1, ['padre_id' => $finder->id]);
                            $father = Tercero::find($finder->id);
                            $father->numero_referidos = $father->numero_referidos +1;
                            $father->save();

                            $findcustomer = Customer::where('customer_id', $father->customer_id)->where('network_id', 1)->first();

                            if (count($findcustomer) > 0) {

                                $res = $client->request('get', $api_url . '/admin/customers/' . $father->customer_id . '/metafields.json');
                                $metafields = json_decode($res->getBody(), true);

                                if (count($metafields['metafields']) > 0) {

                                    foreach ($metafields['metafields'] as $metafield) {

                                        if ($metafield['key'] === 'referidos') {
                                            $res = $client->request('put', $api_url . '/admin/customers/' . $father->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                    'form_params' => array(
                                                        'metafield' => array(
                                                            'namespace' => 'customers',
                                                            'key' => 'referidos',
                                                            'value' => ($father->numero_referidos == null || $father->numero_referidos == 0) ? 0 : $father->numero_referidos,
                                                            'value_type' => 'integer'
                                                        )
                                                    )
                                                )
                                            );

                                            array_push($results, json_decode($res->getBody(), true));
                                        }

                                        if ($metafield['key'] === 'compras') {
                                            $res = $client->request('put', $api_url . '/admin/customers/' . $father->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                    'form_params' => array(
                                                        'metafield' => array(
                                                            'namespace' => 'customers',
                                                            'key' => 'compras',
                                                            'value' => ($father->numero_ordenes_referidos == null || $father->numero_ordenes_referidos == 0) ? 0 : $father->numero_ordenes_referidos,
                                                            'value_type' => 'integer'
                                                        )
                                                    )
                                                )
                                            );

                                            array_push($results, json_decode($res->getBody(), true));
                                        }

                                        if ($metafield['key'] === 'valor') {
                                            $res = $client->request('put', $api_url . '/admin/customers/' . $father->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                    'form_params' => array(
                                                        'metafield' => array(
                                                            'namespace' => 'customers',
                                                            'key' => 'valor',
                                                            'value' => '' . ($father->total_price_orders == null || $father->total_price_orders == 0) ? 0 : number_format($father->total_price_orders * 0.05) . '',
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


                    } else {

                        $tercero->networks()->attach(1, ['padre_id' => 26]);

                        $father = Tercero::find(26);
                        $father->numero_referidos = $father->numero_referidos + 1;
                        $father->save();

                        $findcustomer = Customer::where('customer_id', $father->customer_id)
                            ->where('email', $father->email)
                            ->first();

                        if (count($findcustomer) > 0) {

                            $res = $client->request('get', $api_url . '/admin/customers/' . $father->customer_id . '/metafields.json');
                            $metafields = json_decode($res->getBody(), true);

                            if (isset($metafields['metafields']) && count($metafields['metafields']) > 0) {

                                foreach ($metafields['metafields'] as $metafield) {

                                    if ($metafield['key'] === 'referidos') {
                                        $res = $client->request('put', $api_url . '/admin/customers/' . $father->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                'form_params' => array(
                                                    'metafield' => array(
                                                        'namespace' => 'customers',
                                                        'key' => 'referidos',
                                                        'value' => ($father->numero_referidos == null || $father->numero_referidos == 0) ? 0 : $father->numero_referidos,
                                                        'value_type' => 'integer'
                                                    )
                                                )
                                            )
                                        );

                                        array_push($results, json_decode($res->getBody(), true));
                                    }

                                    if ($metafield['key'] === 'compras') {
                                        $res = $client->request('put', $api_url . '/admin/customers/' . $father->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                'form_params' => array(
                                                    'metafield' => array(
                                                        'namespace' => 'customers',
                                                        'key' => 'compras',
                                                        'value' => ($father->numero_ordenes_referidos == null || $father->numero_ordenes_referidos == 0) ? 0 : $father->numero_ordenes_referidos,
                                                        'value_type' => 'integer'
                                                    )
                                                )
                                            )
                                        );

                                        array_push($results, json_decode($res->getBody(), true));
                                    }

                                    if ($metafield['key'] === 'valor') {
                                        $res = $client->request('put', $api_url . '/admin/customers/' . $father->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                'form_params' => array(
                                                    'metafield' => array(
                                                        'namespace' => 'customers',
                                                        'key' => 'valor',
                                                        'value' => '' . ($father->total_price_orders == null || $father->total_price_orders == 0) ? 0 : number_format($father->total_price_orders * 0.05) . '',
                                                        'value_type' => 'string'
                                                    )
                                                )
                                            )
                                        );

                                        array_push($results, json_decode($res->getBody(), true));
                                    }
                                }
                            }

                            if (isset($metafields['metafields']) && count($metafields['metafields']) == 0) {

                                $resd = $client->request('post', $api_url . '/admin/customers/' . $father->customer_id . '/metafields.json', array(
                                    'form_params' => array(
                                        'metafield' => array(
                                            'namespace' => 'customers',
                                            'key' => 'referidos',
                                            'value' => ($father->numero_referidos == null) ? 0 : $father->numero_referidos,
                                            'value_type' => 'integer'
                                        )
                                    )
                                ));

                                $headers = $resd->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                $x = explode('/', $headers[0]);
                                $diferencia = $x[1] - $x[0];
                                if ($diferencia < 10) {
                                    usleep(500000);
                                }

                                array_push($results, json_decode($resd->getBody(), true));

                                $rese = $client->request('post', $api_url . '/admin/customers/' . $father->customer_id . '/metafields.json', array(
                                    'form_params' => array(
                                        'metafield' => array(
                                            'namespace' => 'customers',
                                            'key' => 'compras',
                                            'value' => ($father->numero_ordenes_referidos == null) ? 0 : $father->numero_ordenes_referidos,
                                            'value_type' => 'integer'
                                        )
                                    )
                                ));

                                $headers = $rese->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                $x = explode('/', $headers[0]);
                                $diferencia = $x[1] - $x[0];
                                if ($diferencia < 10) {
                                    usleep(500000);
                                }

                                array_push($results, json_decode($rese->getBody(), true));

                                $resf = $client->request('post', $api_url . '/admin/customers/' . $father->customer_id . '/metafields.json', array(
                                    'form_params' => array(
                                        'metafield' => array(
                                            'namespace' => 'customers',
                                            'key' => 'valor',
                                            'value' => '' . ($father->ganacias == null) ? 0 : number_format($father->ganacias) . '',
                                            'value_type' => 'string'
                                        )
                                    )
                                ));

                                $headers = $resf->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                $x = explode('/', $headers[0]);
                                $diferencia = $x[1] - $x[0];
                                if ($diferencia < 10) {
                                    usleep(500000);
                                }

                                array_push($results, json_decode($resf->getBody(), true));

                                $resg = $client->request('post', $api_url . '/admin/customers/' . $father->customer_id . '/metafields.json', array(
                                    'form_params' => array(
                                        'metafield' => array(
                                            'namespace' => 'customers',
                                            'key' => 'redimir',
                                            'value' => '' . ($father->redimido == null) ? 0 : number_format($father->redimido) . '',
                                            'value_type' => 'string'
                                        )
                                    )
                                ));

                                $headers = $resg->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                $x = explode('/', $headers[0]);
                                $diferencia = $x[1] - $x[0];
                                if ($diferencia < 10) {
                                    usleep(500000);
                                }

                                array_push($results, json_decode($resg->getBody(), true));
                            }
                        }
                    }


                    return response()->json(['status' => 'The resource has been created.'], 200);
                } else {
                    return response()->json(['status' => 'The resource was not created successfully'], 200);
                }
            }
        }
    }

    public function meta()
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

                    $update = Tercero::find($find->id);
                    $update->ganacias = $update->total_price_orders * 0.05;
                    $update->save();

                    $res = $client->request('get', $api_url . '/admin/customers/' . $update->customer_id . '/metafields.json', ['delay' => 1, 'timeout' => 1]);
                    $metafields = json_decode($res->getBody(), true);


                    if (isset($metafields['metafields']) && count($metafields['metafields']) == 0) {

                        $resd = $client->request('post', $api_url . '/admin/customers/' . $update->customer_id . '/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace' => 'customers',
                                    'key' => 'referidos',
                                    'value' => ($update->numero_referidos == null) ? 0 : $update->numero_referidos,
                                    'value_type' => 'integer'
                                )
                            )
                        ));

                        $headers = $resd->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                        $x = explode('/', $headers[0]);
                        $diferencia = $x[1] - $x[0];
                        if ($diferencia < 10) {
                            usleep(10000000);
                        }

                        array_push($results, json_decode($resd->getBody(), true));

                        $rese = $client->request('post', $api_url . '/admin/customers/' . $update->customer_id . '/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace' => 'customers',
                                    'key' => 'compras',
                                    'value' => ($update->numero_ordenes_referidos == null) ? 0 : $update->numero_ordenes_referidos,
                                    'value_type' => 'integer'
                                )
                            )
                        ));

                        $headers = $rese->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                        $x = explode('/', $headers[0]);
                        $diferencia = $x[1] - $x[0];
                        if ($diferencia < 10) {
                            usleep(10000000);
                        }

                        array_push($results, json_decode($rese->getBody(), true));

                        $resf = $client->request('post', $api_url . '/admin/customers/' . $update->customer_id . '/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace' => 'customers',
                                    'key' => 'valor',
                                    'value' => '' . ($update->ganacias == null ) ? 0 : number_format($update->ganacias) . '',
                                    'value_type' => 'string'
                                )
                            )
                        ));

                        $headers = $resf->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                        $x = explode('/', $headers[0]);
                        $diferencia = $x[1] - $x[0];
                        if ($diferencia < 10) {
                            usleep(10000000);
                        }

                        array_push($results, json_decode($resf->getBody(), true));

                        $resg = $client->request('post', $api_url . '/admin/customers/' . $tercero->customer_id . '/metafields.json', array(
                            'form_params' => array(
                                'metafield' => array(
                                    'namespace' => 'customers',
                                    'key' => 'redimir',
                                    'value' => '' . ($update->redimido == null ) ? 0 : number_format($update->redimido) . '',
                                    'value_type' => 'string'
                                )
                            )
                        ));

                        $headers = $resg->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                        $x = explode('/', $headers[0]);
                        $diferencia = $x[1] - $x[0];
                        if ($diferencia < 10) {
                            usleep(10000000);
                        }

                        array_push($results, json_decode($resg->getBody(), true));
                    }
                }
            }
        }

        return $results;
    }

    public function metadelete()
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

                    $update = Tercero::find($find->id);


                    $res = $client->request('get', $api_url . '/admin/customers/' . $update->customer_id . '/metafields.json', ['delay' => 1, 'timeout' => 1]);
                    $metafields = json_decode($res->getBody(), true);


                    if (isset($metafields['metafields']) && count($metafields['metafields']) > 0) {

                        foreach ($metafields['metafields'] as $metafield) {

                            $res = $client->request('delete', $api_url . '/admin/metafields/'. $metafield['id'] .'.json');
                            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];

                            $x = explode('/', $headers[0]);
                            $diferencia = $x[1] - $x[0];

                            if ($diferencia < 10) {
                                usleep(10000000);
                            }
                            array_push($results, json_decode($res->getBody(), true));
                        }
                    }

                }
            }
        }

        return $results;

    }

    public function gifts()
    {
        ini_set('memory_limit','1000M');

        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

        $terceros = Tercero::all();

        foreach ($terceros as $tercero) {

            if ($tercero->ganacias >= 1000) {

                $orders_save = array();
                $valor_redimir = 0;
                $redimir = 0;
                $sons = DB::table('terceros_networks')->select('customer_id')->where('padre_id', $tercero->id)->get();

                foreach ($sons as $son) {
                    $searchemail = Tercero::find($son->customer_id);

                    if(isset($searchemail->email)) {
                        $orders = Order::where('email', $searchemail->email)
                            ->where('financial_status', 'paid')
                            ->where('redimir', false)
                            ->orWhere('redimir', null)
                            ->get();
                        foreach ($orders as $order) {
                            $redimir = $redimir + $order->total_price;
                            //$findorder = Order::find($order->id);
                            //$findorder->redimir = true;
                            //$findorder->save();
                            array_push($orders_save, ['order_id' => $order->order_id, 'name' => $order->name]);
                        }

                        $valor_redimir = $redimir * 0.05;
                    }







                }

                if ($valor_redimir > 0) {

                    $tercero_update = Tercero::find($tercero->id);
                    //$tercero_update->redimido =  $valor_redimir;
                    //$tercero_update->save();

                    $send = [
                        'form_params' => [
                            'gift_card' => [
                                "note" => "This is a note",
                                "initial_value" => $valor_redimir,
                                "template_suffix" => "gift_cards.birthday.liquid",
                                "currency" => "COP",
                                "customer_id" => $tercero_update->customer_id,
                            ]
                        ]
                    ];

                    /*$res = $client->request('post', $api_url . '/admin/gift_cards.json', $send);

                    $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                    $x = explode('/', $headers[0]);
                    $diferencia = $x[1] - $x[0];

                    if ($diferencia < 10) {
                        usleep(500000);
                    }

                    $result = json_decode($res->getBody(), true);*/

                    //if (isset($result['gift_card']) && count($result['gift_card']) > 0) {

                    $commision = Commision::create([
                        'tercero_id' => $tercero_update->id,
                        'gift_card' => $send,
                        'orders' => $orders_save,
                        'value' => $valor_redimir,
                        'bitacora' => [
                            'ip' => gethostname(),
                            'user' => get_current_user()
                        ]
                    ]);

                    /*if ($commision) {
                        $resa = $client->request('get', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields.json');
                        $metafields = json_decode($resa->getBody(), true);
                        $results = array();

                        if (count($metafields['metafields']) > 0 && count($metafields['metafields']) == 4) {

                            foreach ($metafields['metafields'] as $metafield) {

                                if (isset($metafield['key']) && $metafield['key'] === 'referidos') {
                                    $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                            'form_params' => array(
                                                'metafield' => array(
                                                    'namespace' => 'customers',
                                                    'key' => 'referidos',
                                                    'value' => ($tercero_update->numero_referidos == null || $tercero_update->numero_referidos == 0) ? 0 : $tercero_update->numero_referidos,
                                                    'value_type' => 'integer'
                                                )
                                            )
                                        )
                                    );

                                    $headers = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                    $x = explode('/', $headers[0]);
                                    $diferencia = $x[1] - $x[0];

                                    if ($diferencia < 10) {
                                        usleep(500000);
                                    }

                                    array_push($results, json_decode($resb->getBody(), true));
                                }

                                if (isset($metafield['key']) && $metafield['key'] === 'compras') {
                                    $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/' . $metafield['id'] . '.json', array(
                                            'form_params' => array(
                                                'metafield' => array(
                                                    'namespace' => 'customers',
                                                    'key' => 'compras',
                                                    'value' => ($tercero_update->numero_ordenes_referidos == null || $tercero_update->numero_ordenes_referidos == 0) ? 0 : $tercero_update->numero_ordenes_referidos,
                                                    'value_type' => 'integer'
                                                )
                                            )
                                        )
                                    );

                                    $headers = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                    $x = explode('/', $headers[0]);
                                    $diferencia = $x[1] - $x[0];

                                    if ($diferencia < 10) {
                                        usleep(500000);
                                    }

                                    array_push($results, json_decode($resb->getBody(), true));
                                }

                                if (isset($metafield['key']) && $metafield['key'] === 'valor') {
                                    $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/' . $metafield['id'] . '.json', array(
                                            'form_params' => array(
                                                'metafield' => array(
                                                    'namespace' => 'customers',
                                                    'key' => 'valor',
                                                    'value' => '' . ($tercero_update->ganacias == null || $tercero_update->ganacias == 0) ? 0 : number_format($tercero_update->ganacias) . '',
                                                    'value_type' => 'string'
                                                )
                                            )
                                        )
                                    );

                                    $headers = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                    $x = explode('/', $headers[0]);
                                    $diferencia = $x[1] - $x[0];

                                    if ($diferencia < 10) {
                                        usleep(500000);
                                    }

                                    array_push($results, json_decode($resb->getBody(), true));
                                }

                                if (isset($metafield['key']) && $metafield['key'] === 'redimir') {

                                    $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/' . $metafield['id'] . '.json', array(
                                            'form_params' => array(
                                                'metafield' => array(
                                                    'namespace' => 'customers',
                                                    'key' => 'redimir',
                                                    'value' => '' . number_format($valor_redimir) . '',
                                                    'value_type' => 'string'
                                                )
                                            )
                                        )
                                    );
                                    $headers = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                    $x = explode('/', $headers[0]);
                                    $diferencia = $x[1] - $x[0];

                                    if ($diferencia < 10) {
                                        usleep(500000);
                                    }

                                    array_push($results, json_decode($resb->getBody(), true));
                                }
                            }
                        }

                        if (count($metafields['metafields']) > 0 && count($metafields['metafields']) == 3) {

                            foreach ($metafields['metafields'] as $metafield) {

                                if (isset($metafield['key']) && $metafield['key'] === 'referidos') {
                                    $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/' . $metafield['id'] . '.json', array(
                                            'form_params' => array(
                                                'metafield' => array(
                                                    'namespace' => 'customers',
                                                    'key' => 'referidos',
                                                    'value' => ($tercero_update->numero_referidos == null || $tercero_update->numero_referidos == 0) ? 0 : $tercero_update->numero_referidos,
                                                    'value_type' => 'integer'
                                                )
                                            )
                                        )
                                    );

                                    $headers = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                    $x = explode('/', $headers[0]);
                                    $diferencia = $x[1] - $x[0];

                                    if ($diferencia < 10) {
                                        usleep(500000);
                                    }

                                    array_push($results, json_decode($resb->getBody(), true));
                                }

                                if (isset($metafield['key']) && $metafield['key'] === 'compras') {
                                    $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/' . $metafield['id'] . '.json', array(
                                            'form_params' => array(
                                                'metafield' => array(
                                                    'namespace' => 'customers',
                                                    'key' => 'compras',
                                                    'value' => ($tercero_update->numero_ordenes_referidos == null || $tercero_update->numero_ordenes_referidos == 0) ? 0 : $tercero_update->numero_ordenes_referidos,
                                                    'value_type' => 'integer'
                                                )
                                            )
                                        )
                                    );

                                    $headers = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                    $x = explode('/', $headers[0]);
                                    $diferencia = $x[1] - $x[0];

                                    if ($diferencia < 10) {
                                        usleep(500000);
                                    }

                                    array_push($results, json_decode($resb->getBody(), true));
                                }

                                if (isset($metafield['key']) && $metafield['key'] === 'valor') {
                                    $resb = $client->request('put', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields/' . $metafield['id'] . '.json', array(
                                            'form_params' => array(
                                                'metafield' => array(
                                                    'namespace' => 'customers',
                                                    'key' => 'valor',
                                                    'value' => '' . ($tercero_update->ganacias == null || $tercero_update->ganacias == 0) ? 0 : number_format($tercero_update->ganacias) . '',
                                                    'value_type' => 'string'
                                                )
                                            )
                                        )
                                    );

                                    $headers = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                    $x = explode('/', $headers[0]);
                                    $diferencia = $x[1] - $x[0];

                                    if ($diferencia < 10) {
                                        usleep(500000);
                                    }

                                    array_push($results, json_decode($resb->getBody(), true));
                                }
                            }

                            $resg = $client->request('post', $api_url . '/admin/customers/'. $tercero_update->customer_id .'/metafields.json', array(
                                'form_params' => array(
                                    'metafield' => array(
                                        'namespace' => 'customers',
                                        'key' => 'redimir',
                                        'value' => '' .  number_format($valor_redimir) . '',
                                        'value_type' => 'string'
                                    )
                                )
                            ));

                            $headers = $resg->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                            $x = explode('/', $headers[0]);
                            $diferencia = $x[1] - $x[0];
                            if ($diferencia < 10) {
                                usleep(500000);
                            }

                            array_push($results, json_decode($resg->getBody(), true));
                        }
                        $redimir = 0;
                        $valor_redimir = 0;
                        $orders_save = [];


                    }*/
                    //}
                }
            }
        }
    }

    public function mercado()
    {
        define('CLIENT_ID', "7134341661319721");
        define('CLIENT_SECRET', "b7cQUIoU5JF4iWVvjM0w1YeX4b7VwLpw");

        $mp = new MP(CLIENT_ID, CLIENT_SECRET);

        define('payments', '/v1/payments/search?external_reference=');
        define('access', '&access_token=');
        define('ACCESS_TOKEN', $mp->get_access_token());

        $orders = Order::where('financial_status', 'pending')->get();
        $contador = 0;
        $contadora = 0;

        foreach ($orders as $order) {

            $results = Logorder::where('order_id', $order->order_id)->where('checkout_id', $order->checkout_id)->first();

            if (count($results) == 0) {

                $contador ++;

                if ($contador  == 300) {
                    usleep(500000);
                    $contador = 0;
                }

                $result = $mp->get(payments . $order->checkout_id . access . ACCESS_TOKEN);

                if (isset($result['response']['results']) && count($result['response']['results']) > 0) {

                    Logorder::create([
                        'order_id' => $order->order_id,
                        'checkout_id' => $order->checkout_id,
                        'value' => $order->total_price,
                        'status_shopify' => $order->financial_status,
                        'status_mercadopago' => $result['response']['results'][0]['status'],
                        'payment_method_id' => $result['response']['results'][0]['payment_method_id'],
                        'payment_type_id' => $result['response']['results'][0]['payment_type_id']
                    ]);
                }
            } else {

                $contadora ++;

                if ($contadora  == 300) {
                    usleep(500000);
                    $contadora = 0;
                }

                $result = $mp->get(payments . $order->checkout_id . access . ACCESS_TOKEN);

                if (isset($result['response']['results']) && count($result['response']['results']) > 0) {
                    $find = Logorder::where('order_id', $order->order_id)->where('checkout_id', $order->checkout_id)->first();

                    $update = Logorder::find($find->id);
                    $update->status_shopify = $order->financial_status;
                    $update->status_mercadopago = $result['response']['results'][0]['status'];
                    $update->save();
                }

            }
        }

        return response()->json(['status' => 'finished'], 200);
    }
}
