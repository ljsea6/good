<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\Entities\Tercero;

use DB;


class Metafields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:metafields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de metadatos a shopify';

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

        $this->info('metafields enviados correctamente');
    }
}
