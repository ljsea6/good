<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Product;
use Carbon\Carbon;


class ProductsController extends Controller
{
    /**
     * Undocumented function
     *
     * @return Products
     */

    public function index()
    {
        $results= Product::all();
        $products = array_map(function ($value) {
            return ['product' => $value];
        }, $results->toArray());
        return $products;
    }

    public function getProducts() 
    {

        $totalProducts = array();

        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com/';
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api_url . '/admin/products/count.json');
        $countProducts = json_decode($res->getBody(), true);
        return $countProducts;

        $pagesNumber = (int)$countProducts['count']/50;
        $number = explode( '.', $pagesNumber);
        $entera = (int)$number[0];
        $decimal = (int)$number[1];




        if($decimal !== 0) {
            $entera = $entera + 1;
        }

        for ($i = 1; $i <= $entera; $i++) {
            $res = $client->request('GET', $api_url . '/admin/products.json?page=' . $i);
            $results = json_decode($res->getBody(), true);
            array_push($totalProducts, $results);
        }

        //return count($totalProducts[1]['products']);

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

            if(!$response) {
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

        }
        //return $products;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ProductsWithVariantsPriceZero()
    {
        $products = Product::all();

        $zero = array();
        $productsFinder = array();

        foreach ($products as $product){
            $variants = $product->variants;
            for ($i = 0; $i < count($variants); $i++) {
                $decimal = explode('.', $variants[0]['price']);
                if ((int)$decimal[0] <= 3000) {
                    array_push($zero, $variants[0]['product_id']);
                    break;
                }
            }
        }

        for ($i = 0; $i < count($zero); $i++) {
            $product = Product::find($zero[$i]);
            array_push($productsFinder, $product);
        }


        return view('admin.productos.home', compact('productsFinder'));
    }

    public function ProductsWithVariantsPriceNotZero()
    {
        $products = Product::all();

        $zero = array();
        $productsFinder = array();

        foreach ($products as $product){
            $variants = $product->variants;
            for ($i = 0; $i < count($variants); $i++) {
                $decimal = explode('.', $variants[0]['price']);
                if ((int)$decimal[0] !== 0) {
                    array_push($zero, $variants[0]['product_id']);
                    break;
                }
            }
        }

        for ($i = 0; $i < count($zero); $i++) {
            $product = Product::find($zero[$i]);
            array_push($productsFinder, $product);
        }


        return view('admin.productos.home', compact('productsFinder'));
    }

    public function countAllProducts()
    {
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com/';
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api_url . '/admin/products/count.json');
        $results = json_decode($res->getBody(), true);
        return $results;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
