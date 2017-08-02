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

    function shopify_call($token, $shop, $api_endpoint, $query = array(), $headers = array(), $method = 'GET') {

        // Build URL
        $url = "https://" . $shop . ".myshopify.com" . $api_endpoint;
        if (is_null($query)) $url = $url . "?" . http_build_query($query);

        // Configure cURL
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        // Setup headers
        $request_headers[] = "Content-type: text/plain";
        $request_headers[] = "X-Shopify-Access-Token: " . $token;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

        // Send request to Shopify and capture any errors
        $response = curl_exec($curl);
        $error_number = curl_errno($curl);
        $error_message = curl_error($curl);

        // Close cURL to be nice
        curl_close($curl);

        // Return an error is cURL has a problem
        if ($error_number) {
            return 'error';
        } else {

            // No error, return Shopify's response by parsing out the body
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
            return $response[1];

        }

    }

    public function getCostumers()
    {
        $totalOrders = array();
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $result_url = explode('.', $api_url);

        

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
                $res = $client->request('GET', $api_url . '/admin/orders.json?limit=250&&financial_status=paid&&page=' . $i);
                $results = json_decode($res->getBody(), true);
                array_push($totalOrders, $results);
            }

            $resultsOrders = array();

            foreach ($totalOrders as $order) {
                foreach ($order['orders'] as $value){
                    array_push($resultsOrders, $value);
                }
                
            }
            
            return count($resultsOrders);
        
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
