<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\Entities\Network;
use Carbon\Carbon;
use App\Entities\Tercero;
use DB;

class GetCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para obtener todos los clientes de la API shopify';

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

        $totalCustomers = array();

        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $result_url = explode('.', $api_url);

        if($result_url[1] == 'myshopify') {

                $res = $client->request('GET', $api_url . '/admin/customers/count.json');
                $countCustomers = json_decode($res->getBody(), true);


                $pagesNumber = (int)$countCustomers['count']/250;
                $number = explode( '.', $pagesNumber);
                $entera = (int)$number[0];
                $decimal = (int)$number[1];


                if($decimal !== 0) {
                    $entera = $entera + 1;
                }

                for ($i = 1; $i <= $entera; $i++) {
                    $res = $client->request('GET', $api_url . '/admin/customers.json?limit=250&&page=' . $i);
                    $results = json_decode($res->getBody(), true);
                    array_push($totalCustomers, $results);
                }


                $resultsCustomers = array();


                for ($j = 0; $j < count($totalCustomers); $j++) {
                    $aux = $totalCustomers[$j]['customers'];
                    for ($i = 0; $i < count($aux); $i++) {
                        array_push($resultsCustomers, $aux[$i]);
                    }

                }

                $customers = array_map(function ($value) {
                    return ['customer' => $value];
                }, $resultsCustomers);

                $network_id = Network::select('id')->where('name', 'shopify')->get();

                foreach ($customers as $customer) {
                    $response = Customer::where('network_id', $network_id[0]['id'])
                        ->where('customer_id', $customer['customer']['id'])
                        ->get();

                    if(count($response) == 0) {

                        Customer::create([
                            'accepts_marketing' => $customer['customer']['accepts_marketing'],
                            'addresses' => $customer['customer']['addresses'],
                            'created_at' => Carbon::parse($customer['customer']['created_at']),
                            'default_address' => (isset($customer['customer']['default_address'])) ? $customer['customer']['default_address'] : null,
                            'email' => strtolower($customer['customer']['email']),
                            'phone' => $customer['customer']['phone'],
                            'first_name' => $customer['customer']['first_name'],
                            'customer_id' => $customer['customer']['id'],
                            'metafield' => null,
                            'multipass_identifier' => $customer['customer']['multipass_identifier'],
                            'last_name' => strtolower($customer['customer']['last_name']),
                            'last_order_id' => $customer['customer']['last_order_id'],
                            'last_order_name' => $customer['customer']['last_order_name'],
                            'network_id' => $network_id[0]['id'],
                            'note' => $customer['customer']['note'],
                            'orders_count' => $customer['customer']['orders_count'],
                            'state' => $customer['customer']['state'],
                            'tags' => $customer['customer']['tags'],
                            'tax_exempt' => $customer['customer']['tax_exempt'],
                            'total_spent' => $customer['customer']['total_spent'],
                            'updated_at' => Carbon::parse($customer['customer']['updated_at']),
                            'verified_email' => $customer['customer']['verified_email'],
                        ]);


                    }

                }


                $customersresults = Customer::all();


                foreach ($customersresults as $customer) {


                    $result = Tercero::where('network_id', $customer['network_id'])
                        ->where('customer_id', $customer['customer_id'])
                        ->where('email', $customer['email'])
                        ->get();



                    if(count($result) == 0) {
                        $aux = explode('@', strtolower($customer['email']));
                        $tercero = new Tercero();
                        $tercero->nombres = (empty($customer['first_name']) || $customer['first_name'] == null || $customer['first_name'] == '') ? $customer['email'] : $customer['first_name'];
                        $tercero->apellidos = strtolower($customer['last_name']);
                        $tercero->email = strtolower($customer['email']);
                        $tercero->usuario = strtolower($customer['email']);
                        $tercero->contraseña = bcrypt($aux[0]);
                        $tercero->tipo_id = 1;
                        $tercero->customer_id = $customer['customer_id'];
                        $tercero->network_id = $customer['network_id'];
                        $tercero->save();
                    }
                }
            
                
        }
        
        $this->info('Los clientes han sido descargados correctamente');
    }
    
    
}
