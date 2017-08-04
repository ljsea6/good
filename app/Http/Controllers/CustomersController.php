<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 500);

use App\Customer;
use App\Order;
use App\Entities\Network;
use App\Entities\Tercero;
use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

  

    public function getCostumers()
    {
            
            $totals = array();
            $orders = Order::get();

            foreach ($orders as $order) {

                $line_item = $order['line_items'];

                if ($line_item[0]['product_id'] === null) {

                    array_push($totals, $order);
                }
            }
            
            $report = array();
            
            foreach ($totals as $total) {
             
                $aux = [
                    'id' => $total['order_id'],
                    'name' => $total['name'],
                    
                ];
               
                array_push($report, $aux);
            }
            
            $totalOrders = array();

            $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
            $client = new \GuzzleHttp\Client();
            $result_url = explode('.', $api_url);

    

            $res = $client->request('GET', $api_url . '/admin/orders/count.json?financial_status=paid');
            $countOrders = json_decode($res->getBody(), true);
            return $countOrders;

            $pagesNumber = (int)$countOrders['count']/250;
            $number = explode( '.', $pagesNumber);
            $entera = (int)$number[0];
            $decimal = (int)$number[1];

            if($decimal !== 0) {
                $entera = $entera + 1;
            }

            for ($i = 1; $i <= $entera; $i++) {
                $res = $client->request('GET', $api_url . '/admin/orders.json?limit=250&&financial_status=any&&page=' . $i);
                $results = json_decode($res->getBody(), true);
                array_push($totalOrders, $results);
            }

            $resultsOrders = array();
            
            foreach ($totalOrders as $order) {
                foreach ($order['orders'] as $value){
                    array_push($resultsOrders, $value);
                }
                
            }
            
            /**
             * 
             *
        
                        $tercero = new Tercero();
                        $tercero->id = 7;
                        $tercero->nombres = 'goldfish';
                        $tercero->apellidos = 'goldfish@';
                        $tercero->email = 'goldfish';
                        $tercero->usuario = 'goldfish';
                        $tercero->contraseña = bcrypt('goldfish');
                        $tercero->tipo_id = 1;
                        $tercero->customer_id = 7;
                        $tercero->network_id = 1;
                        $tercero->save();
             * 
             * @return type
             */

    }
       
    

    public function index()
    {
        return Test::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
