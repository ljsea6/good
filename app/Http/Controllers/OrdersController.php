<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $totalOrders = array();

        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com/';
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api_url . '/admin/orders/count.json');
        $countOrders = json_decode($res->getBody(), true);

        $pagesNumber = (int)$countOrders['count']/250;
        $number = explode( '.', $pagesNumber);
        $entera = (int)$number[0];
        $decimal = (int)$number[1];

        if($decimal !== 0) {
            $entera = $entera + 1;
        }

        for ($i = 1; $i <= $entera; $i++) {
            $res = $client->request('GET', $api_url . '/admin/orders.json?limit=250&&page=' . $i);
            $results = json_decode($res->getBody(), true);
            array_push($totalOrders, $results);
        }



        $resultsOrders = array();

        foreach ($totalOrders[0]['orders'] as $order) {
            array_push($resultsOrders, $order);
        }


        $orders = array_map(function ($value) {
            return  $value;
        }, $resultsOrders);


        return $orders;
        
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
        $orders = Order::where('customer_id', (int)$id)->get();
        return view('admin.orders.show', compact('orders'));

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
