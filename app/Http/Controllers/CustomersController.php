<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 300);

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
                       
                $referidosaux = array();
                
                $tercerosaux = Tercero::select('id', 'identificacion', 'nombres', 'apellidos', 'email')
                    ->where('state', true)
                    ->orderBy('id')
                    ->get();
               
                foreach ($tercerosaux as $tercero) {
                    $sons = DB::table('terceros_networks')
                            ->where('terceros_networks.padre_id', '=', $tercero['id'])
                            ->get();
                    $find = Tercero::find($tercero['id']); 
                    $find->numero_referidos = count($sons);
                   
                    $i = 0;
                    foreach ($sons as $son)
                    {
                        $result = Tercero::find($son->customer_id);
                        
                        if ($result->state) {
                            $resultOrders = DB::table('orders')
                                    ->where('orders.customer_id', $result->customer_id)
                                    ->where('orders.email', $result->email)
                                    ->get();
                            $i = $i + count($resultOrders);
                        }
                        
                    }
                    $find->numero_ordenes_referidos = $i;
                    $find->save();
                }
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
