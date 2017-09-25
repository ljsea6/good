<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Product;
use Carbon\Carbon;

class GetProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para obtener todos los productos de la API shopify';

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
        $totalProducts = array();

        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com/';
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api_url . 'admin/products/count.json');
        $countProducts = json_decode($res->getBody(), true);

        $pagesNumber = (int)$countProducts['count']/250;
        $number = explode( '.', $pagesNumber);
        $entera = (int)$number[0];
        $decimal = (int)$number[1];





        if($decimal !== 0) {
            $entera = $entera + 1;
        }

        for ($i = 1; $i <= $entera; $i++) {
            $res = $client->request('GET', $api_url . 'admin/products.json?limit=250&&page=' . $i);
            $results = json_decode($res->getBody(), true);
            array_push($totalProducts, $results);
        }


        $resultsProducts = array();


        for ($j = 0; $j < count($totalProducts); $j++) {
            $aux = $totalProducts[$j]['products'];
            for ($i = 0; $i < count($aux); $i++) {
                array_push($resultsProducts, $aux[$i]);
            }

        }

        $products = array_map(function ($value) {
            return ['product' => $value];
        }, $resultsProducts);


        foreach ($products as $product) {
            $response = Product::find((int)$product['product']['id']);

            if(count($response) == 0) {
                Product::create([
                    'body_html' => $product['product']['body_html'],
                    'created_at' => Carbon::parse($product['product']['created_at']),
                    'handle' => $product['product']['handle'],
                    'id' => $product['product']['id'],
                    'image' => $product['product']['image'],
                    'images' => $product['product']['images'],
                    'options' => $product['product']['options'],
                    'product_type' => $product['product']['product_type'],
                    'published_at' => Carbon::parse($product['product']['published_at']),
                    'published_scope' => $product['product']['published_scope'],
                    'tags' => $product['product']['tags'],
                    'template_suffix' => ($product['product']['template_suffix'] !== null ) ? $product['product']['template_suffix'] : null,
                    'title' => $product['product']['title'],
                    'metafields_global_title_tag' => (isset($product['product']['metafields_global_title_tag'])) ? $product['product']['metafields_global_title_tag'] : null,
                    'metafields_global_description_tag' => (isset($product['product']['metafields_global_description_tag'])) ? $product['product']['metafields_global_description_tag'] : null,
                    'updated_at' => Carbon::parse($product['product']['updated_at']),
                    'variants' => $product['product']['variants'],
                    'vendor' => $product['product']['vendor'],
                ]);
            }

            if (count($response) > 0 && count($response->image) == 0 ) {

                $response->image = $product['product']['image'];
                $response->images = $product['product']['images'];
                $response->save();
            }

        }

        $this->info('Los productos han sido descargados correctamente');
    }
}
