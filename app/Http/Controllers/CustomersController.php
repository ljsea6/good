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
        $networks = Network::select('id', 'name')->get();

        foreach ($networks as $network) {

            if ($network['name'] == 'shopify') {

                $terceros = Tercero::where('network_id', $network['id'])
                                    ->where('state', true)
                                    ->orderby('id')
                                    ->get();
                                            
                if (count($terceros) > 0) {

                    foreach ($terceros as $tercero) {

                        $result = DB::table('terceros_networks')
                            ->where('network_id', $network['id'])
                            ->where('customer_id', $tercero['id'])
                            ->select('*')
                            ->get();

                        if(count($result) == 0){

                            $finder = Tercero::find($tercero['id']);

                             if (empty($finder['apellidos']) || $finder['apellidos'] === null || $finder['apellidos'] === '') {

                                $finder->networks()->attach($network['id'], ['padre_id' => null]);
                             }

                            if (!empty($finder['apellidos'])) {

                                $father = Tercero::where('email', $finder['apellidos'])->select('id')->get();

                                if (count($father) > 0) {
                                    $finder->networks()->attach($network['id'], ['padre_id' => $father[0]['id']]);
                                } else {
                                    $finder->networks()->attach($network['id'], ['padre_id' => null]);
                                }
                            }     
                        }
                        
                    }
                }

            }

            if($network['name'] == 'to go') {

                $terceros =  Tercero::where('network_id', $network['id'])->get();

                if(count($terceros) > 0){
                    return 'to go tiene.';
                }
            }
        }
        /*
         * 
         
        
        $totalOrders = array();

        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $result_url = explode('.', $api_url);

        if($result_url[1] == 'myshopify') {

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
                $res = $client->request('GET', $api_url . '/admin/orders.json?limit=250&&status=any&&page=' . $i);
                $results = json_decode($res->getBody(), true);
                array_push($totalOrders, $results);
            }

            $resultsOrders = array();

          
            foreach ($totalOrders as $order) {
                foreach ($order['orders'] as $value){
                    array_push($resultsOrders, $value);
                }
                
            }

            


            $network_id = Network::select('id')->where('name', 'shopify')->get();
            
       
            $i = 0;
            foreach ($resultsOrders as $order) {

                if ($order['financial_status'] == 'paid') {
                   $i++;
                   /**
                    /
                   $response = Order::where('network_id', $network_id[0]['id'])->where('order_id', $order['id'])->get();
                    
                    if(count($response) > 0) {
                        DB::table('orders')
                                ->where('network_id', $network_id[0]['id'])
                                ->where('order_id', $order['id'])
                                ->update(['financial_status' => $order['financial_status']]);
                    }

                    if(count($response) == 0) {

                        Order::create([
                            'billing_address' => $order['billing_address'],
                            'browser_ip' => $order['browser_ip'],
                            'buyer_accepts_marketing' => $order['buyer_accepts_marketing'],
                            'cancel_reason' => $order['cancel_reason'],
                            'cancelled_at' => Carbon::parse($order['cancelled_at']),
                            'cart_token' => $order['cart_token'],
                            'client_details' => $order['client_details'],
                            'closed_at' => Carbon::parse($order['closed_at']),
                            'currency' => $order['currency'],
                            'customer_id' => $order['customer']['id'],
                            'discount_codes' => $order['discount_codes'],
                            'email' => strtolower($order['email']),
                            'financial_status' => $order['financial_status'],
                            'fulfillments' => $order['fulfillments'],
                            'fulfillment_status' => $order['fulfillment_status'],
                            'tags' => $order['tags'],
                            'gateway' => $order['gateway'],
                            'landing_site' => $order['landing_site'],
                            'landing_site_ref' => $order['landing_site_ref'],
                            'line_items' => $order['line_items'],
                            'location_id' => $order['location_id'],
                            'name' => $order['name'],
                            'network_id' => (int)$network_id['id'],
                            'note' => $order['note'],
                            'note_attributes' => $order['note_attributes'],
                            'number' => $order['number'],
                            'order_id' => (int)$order['id'],
                            'order_number' => $order['order_number'],
                            'payment_details' => null,
                            'payment_gateway_names' => $order['payment_gateway_names'],
                            'phone' => $order['phone'],
                            'processed_at' => Carbon::parse($order['processed_at']),
                            'processing_method' => $order['processing_method'],
                            'referring_site' => $order['referring_site'],
                            'refunds' => $order['refunds'],
                            'shipping_address' => (!empty($order['shipping_address'])) ?$order['shipping_address'] : null,
                            'shipping_lines' => $order['shipping_lines'],
                            'source_name' => $order['source_name'],
                            'subtotal_price' => $order['subtotal_price'],
                            'tax_lines' => $order['tax_lines'],
                            'taxes_included' => $order['taxes_included'],
                            'token' => $order['token'],
                            'total_discounts' => $order['total_discounts'],
                            'total_line_items_price' => $order['total_line_items_price'],
                            'total_price' => $order['total_price'],
                            'total_tax' => $order['total_tax'],
                            'total_weight' => $order['total_weight'],
                            'user_id' => $order['user_id'],
                            'order_status_url' => $order['order_status_url'],
                            'created_at' => Carbon::parse($order['created_at']),
                            'updated_at' => Carbon::parse($order['updated_at']),
                            'test' => $order['test'],
                            'confirmed' => $order['confirmed'],
                            'total_price_usd' => $order['total_price_usd'],
                            'checkout_token' => $order['checkout_token'],
                            'reference' => $order['reference'],
                            'source_identifier' => $order['source_identifier'],
                            'source_url' => $order['source_url'],
                            'device_id' => $order['device_id'],
                            'checkout_id' => $order['checkout_id'],
                        ]);
                    }
                   

                }
            }
            
            return $i;
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
