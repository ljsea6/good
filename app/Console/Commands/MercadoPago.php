<?php

namespace App\Console\Commands;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Illuminate\Console\Command;

use App\Order;
use DB;
use MP;
use MercadoPagoException;

use App\Logorder;

class MercadoPago extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:mercadopago';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para actualizar la informacion de las ordenes de shopify con mercado pago';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    private function parseException($message)
    {
        $error = new \stdClass();
        $error->code = 0;
        $error->detail = '';
        $posA = strpos($message, '-');
        $posB = strpos($message, ':');
        if($posA && $posB) {
            $posA+=2;
            $length = $posB - $posA;
            // get code
            $error->code = substr($message, $posA, $length);
            // get message
            $error->detail = substr($message, $posB+2);
        }
        return $error;
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        define('CLIENT_ID', "7134341661319721");
        define('CLIENT_SECRET', "b7cQUIoU5JF4iWVvjM0w1YeX4b7VwLpw");
        $mp = new MP(CLIENT_ID, CLIENT_SECRET);
        define('payments', '/v1/payments/search?external_reference=');
        define('access', '&access_token=');
        define('ACCESS_TOKEN', $mp->get_access_token());
        $orders = Order::where('financial_status', 'pending')->where('cancelled_at', null)->get();
        $contador = 0;

        Logorder::truncate();

        foreach ($orders as $order) {
            $result = array();
            $results = Logorder::where('order_id', $order->order_id)->where('checkout_id', $order->checkout_id)->first();
            if (count($results) == 0) {
                $contador ++;
                if ($contador  == 300) {
                    usleep(1000000);
                    $contador = 0;
                }
                try {
                    $result = $mp->get(payments . $order->checkout_id . access . ACCESS_TOKEN);
                } catch (MercadoPagoException $e) {
                    $paymentError = new \stdClass();
                    $paymentError->parsed = $this->parseException($e->getMessage());
                    $paymentError->data = $e->getMessage();
                    $paymentError->code = $e->getCode();
                }
                if (isset($result['response']['results']) && count($result['response']['results']) > 0) {
                    Logorder::create([
                        'order_id' => $order->order_id,
                        'checkout_id' => $order->checkout_id,
                        'value' => $order->total_price,
                        'status_shopify' => $order->financial_status,
                        'status_mercadopago' => $result['response']['results'][0]['status'],
                        'payment_method_id' => $result['response']['results'][0]['payment_method_id'],
                        'payment_type_id' => $result['response']['results'][0]['payment_type_id'],
                        'name' => $order->name
                    ]);
                }
            }
            if (count($results) > 0) {
                $contador ++;
                if ($contador  == 300) {
                    usleep(1000000);
                    $contador = 0;
                }
                try {
                    $result = $mp->get(payments . $order->checkout_id . access . ACCESS_TOKEN);
                } catch (MercadoPagoException $e) {
                    $paymentError = new \stdClass();
                    $paymentError->parsed = $this->parseException($e->getMessage());
                    $paymentError->data = $e->getMessage();
                    $paymentError->code = $e->getCode();
                }
                if (isset($result['response']['results']) && count($result['response']['results']) > 0) {
                    $log_update = Logorder::find($results->id);
                    $log_update->status_mercadopago = $result['response']['results'][0]['status'];
                    $log_update->status_shopify = $order->financial_status;
                    $log_update->save();
                }
            }
            $result = array();
        }
        $this->info('Las ordenes han sido actualizadas con la informacion de shopify y mercago pago');
    }
}
