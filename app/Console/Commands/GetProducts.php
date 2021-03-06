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
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com/';
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api_url . 'admin/products/count.json');
        $countProducts = json_decode($res->getBody(), true);

        $this->info('Cantidad Productos' . $countProducts['count']);

        $result = true;
        $h = 1;

        do {
            $this->info('Entrando al do');

            $res = $client->request('GET', $api_url . 'admin/products.json?limit=250&&page=' . $h);

            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 20) {
                $this->info('Durmiendo');
                usleep(20000000);
            }

            $results = json_decode($res->getBody(), true);

            foreach ($results['products'] as  $product) {

                $this->info('entrando al ciclo');

                $response = Product::find($product['id']);

                if(count($response) == 0) {

                    $this->info('creando producto');

                    Product::create([
                        'body_html' => $product['body_html'],
                        'created_at' => Carbon::parse($product['created_at']),
                        'handle' => $product['handle'],
                        'id' => $product['id'],
                        'image' => $product['image'],
                        'images' => $product['images'],
                        'options' => $product['options'],
                        'product_type' => $product['product_type'],
                        'published_at' => Carbon::parse($product['published_at']),
                        'published_scope' => $product['published_scope'],
                        'tags' => $product['tags'],
                        'template_suffix' => ($product['template_suffix'] !== null ) ? $product['template_suffix'] : null,
                        'title' => $product['title'],
                        'metafields_global_title_tag' => (isset($product['metafields_global_title_tag'])) ? $product['metafields_global_title_tag'] : null,
                        'metafields_global_description_tag' => (isset($product['metafields_global_description_tag'])) ? $product['metafields_global_description_tag'] : null,
                        'updated_at' => Carbon::parse($product['updated_at']),
                        'variants' => $product['variants'],
                        'vendor' => $product['vendor'],
                    ]);
                    $this->info('saliendo creando producto');
                }

                if (count($response) > 0 && count($response->image) == 0 ) {
                    $this->info('actualizando producto');

                    $response->image = $product['image'];
                    $response->images = $product['images'];
                    $response->vendor = $product['vendor'];
                    $response->save();

                    $this->info('saliendo actualizando producto');
                }
                $this->info('saliendo el ciclo');
            }

            $h++;

            if (count($results['products']) < 1) {
                $result = false;
            }

            $this->info('Saliendo del do');

        } while($result);

        $this->info('Los productos han sido descargados correctamente');
    }
}
