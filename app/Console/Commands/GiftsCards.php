<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Order;
use App\Entities\Tercero;
use DB;
use App\Commision;


class GiftsCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:giftscards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando que genera los gifts cards para shopify';

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

        $this->info('Los gifts cards han sido creados correctamente');
    }
}
