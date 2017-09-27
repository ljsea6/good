<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;
use Carbon\Carbon;
use App\Entities\Network;
use DB;
use App\Customer;
use App\Entities\Tercero;
use App\Product;
use App\Logorder;
use App\LineItems;
use App\Variant;

class GetOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para obtener todos las ordenes de la API shopify';

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
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

        $res = $client->request('GET', $api_url . '/admin/orders/count.json?status=any');
        $countOrders = json_decode($res->getBody(), true);

        $pagesNumber = (int)$countOrders['count']/250;
        $number = explode( '.', $pagesNumber);
        $entera = (int)$number[0];
        $decimal = (int)$number[1];

        if(isset($decimal) && $decimal != 0) {
            $entera = $entera + 1;
        }

        for ($i = 1; $i <= $entera; $i++) {

            $res = $client->request('GET', $api_url . '/admin/orders.json?limit=250&&status=any&&page=' . $i);
            $results = json_decode($res->getBody(), true);

            foreach ($results['orders'] as  $order) {

                $response = Order::where('network_id', 1)
                    ->where('name', $order['name'])
                    ->where('order_id', $order['id'])
                    ->first();

                if ($order['cancelled_at'] != null && $order['cancel_reason'] != null) {

                    if(count($response) == 0) {

                        $tipo_orden = '';
                        $i = 0;
                        $n = 0;

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) == 0) {
                                    LineItems::create([
                                        'id' => $item['id'],
                                        'variant_id' =>$item['variant_id'],
                                        'title' => $item['title'],
                                        'quantity' =>$item['quantity'],
                                        'price' => $item['price'],
                                        'grams' =>$item['grams'],
                                        'sku' => $item['sku'],
                                        'variant_title' =>$item['variant_title'],
                                        'vendor' => $item['vendor'],
                                        'fulfillment_service' =>$item['fulfillment_service'],
                                        'product_id' => $item['product_id'],
                                        'requires_shipping' =>$item['requires_shipping'],
                                        'taxable' => $item['taxable'],
                                        'gift_card' =>$item['gift_card'],
                                        'pre_tax_price' => $item['pre_tax_price'],
                                        'name' =>$item['name'],
                                        'variant_inventory_management' => $item['variant_inventory_management'],
                                        'properties' =>$item['properties'],
                                        'product_exists' => $item['product_exists'],
                                        'fulfillable_quantity' =>$item['fulfillable_quantity'],
                                        'total_discount' => $item['total_discount'],
                                        'fulfillment_status' =>$item['fulfillment_status'],
                                        'tax_lines' => $item['tax_lines'],
                                        'origin_location' =>$item['origin_location'],
                                        'destination_location' => (isset($item['destination_location'])) ? $item['destination_location'] : null,
                                        'order_name' => $order['name'],
                                        'date_order' =>$order['updated_at'],
                                    ]);
                                }

                                $product = Product::find($item['product_id']);

                                if (strtolower($item['vendor'])  == 'nacional' || strtolower($item['vendor'])  == 'a - nacional') {
                                    $n++;

                                    if (count($product) > 0) {
                                        $product->tipo_producto = 'nacional';
                                        $product->save();
                                    }
                                }
                                if (strtolower($item['vendor'])  != 'nacional' && strtolower($item['vendor'])  != 'a - nacional') {
                                    $i++;

                                    if (count($product) > 0) {
                                        $product->tipo_producto = 'internacional';
                                        $product->save();
                                    }
                                }
                            }
                        }

                        if ($i > 0 && $n > 0) {
                            $tipo_orden .= 'nacional/internacional';
                            $i = 0;
                            $n = 0;
                        }
                        if ($i > 0 && $n == 0) {
                            $tipo_orden .= 'internacional';
                            $i = 0;
                            $n = 0;
                        }
                        if ($i == 0 && $n > 0) {
                            $tipo_orden .= 'nacional';
                            $i = 0;
                            $n = 0;
                        }

                        Order::create([
                            'billing_address' => $order['billing_address'],
                            'browser_ip' => $order['browser_ip'],
                            'buyer_accepts_marketing' => $order['buyer_accepts_marketing'],
                            'cancel_reason' => $order['cancel_reason'],
                            'cancelled_at' => Carbon::parse( $order['cancelled_at']),
                            'cart_token' => $order['cart_token'],
                            'client_details' => $order['client_details'],
                            'closed_at' => $order['closed_at'],
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
                            'network_id' => 1,
                            'note' => $order['note'],
                            'note_attributes' => $order['note_attributes'],
                            'number' => $order['number'],
                            'order_id' => (int) $order['id'],
                            'order_number' => $order['order_number'],
                            'payment_details' => null,
                            'payment_gateway_names' => $order['payment_gateway_names'],
                            'phone' => $order['phone'],
                            'processed_at' => Carbon::parse($order['processed_at']),
                            'processing_method' => $order['processing_method'],
                            'referring_site' => $order['referring_site'],
                            'refunds' => $order['refunds'],
                            'shipping_address' => (!empty($order['shipping_address'])) ? $order['shipping_address'] : null,
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
                            'origin' => 'webhooks',
                            'tipo_orden' => $tipo_orden
                        ]);

                        $tipo_orden = '';

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) == 0) {
                                    LineItems::create([
                                        'id' => $item['id'],
                                        'variant_id' =>$item['variant_id'],
                                        'title' => $item['title'],
                                        'quantity' =>$item['quantity'],
                                        'price' => $item['price'],
                                        'grams' =>$item['grams'],
                                        'sku' => $item['sku'],
                                        'variant_title' =>$item['variant_title'],
                                        'vendor' => $item['vendor'],
                                        'fulfillment_service' =>$item['fulfillment_service'],
                                        'product_id' => $item['product_id'],
                                        'requires_shipping' =>$item['requires_shipping'],
                                        'taxable' => $item['taxable'],
                                        'gift_card' =>$item['gift_card'],
                                        'pre_tax_price' => $item['pre_tax_price'],
                                        'name' =>$item['name'],
                                        'variant_inventory_management' => $item['variant_inventory_management'],
                                        'properties' =>$item['properties'],
                                        'product_exists' => $item['product_exists'],
                                        'fulfillable_quantity' =>$item['fulfillable_quantity'],
                                        'total_discount' => $item['total_discount'],
                                        'fulfillment_status' =>$item['fulfillment_status'],
                                        'tax_lines' => $item['tax_lines'],
                                        'origin_location' =>$item['origin_location'],
                                        'destination_location' => (isset($item['destination_location'])) ? $item['destination_location'] : null,
                                        'order_name' => $order['name'],
                                        'date_order' =>$order['updated_at'],
                                    ]);
                                }
                            }
                        }

                    }

                    if (count($response) > 0) {

                        if ($order['financial_status'] != 'paid') {

                            if ($response->financial_status == "paid" && $response->cancelled_at ==  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();

                                if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                    foreach ($order['line_items'] as $item) {

                                        $variant = Variant::where('product_id', $item['product_id'])->where('variant_id', $item['variant_id'])->first();

                                        if (count($variant) > 0) {
                                            $update = Variant::find($variant->id);
                                            $update->cantidad = $update->cantidad - $item['quantity'];
                                            $update->save();
                                        }


                                        $product = Product::find($item['product_id']);

                                        if (count($product) > 0) {
                                            $product->precio_unidad = $item['price'];
                                            $product->unidades_vendidas = $product->unidades_vendidas - $item['quantity'];
                                            $product->save();
                                        }
                                    }
                                }

                                $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                                if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                    $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                    if ($padre->state) {

                                        $find = Tercero::find($padre->id);
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos - 1;
                                        $find->total_price_orders = $find->total_price_orders - $order['total_price'];
                                        $find->ganacias = $find->total_price_orders * 0.05;
                                        $find->save();

                                        $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                        if (count($customer) > 0) {
                                            $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                            $metafields = json_decode($res->getBody(), true);
                                            $results = array();

                                            if (count($metafields['metafields']) > 0) {

                                                foreach ($metafields['metafields'] as $metafield) {
                                                    if ($metafield['key'] === 'referidos') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'referidos',
                                                                        'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'compras') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'compras',
                                                                        'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'valor') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'valor',
                                                                        'value' => '' . ($find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                        'value_type' => 'string'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ($response->financial_status == "paid" && $response->cancelled_at !=  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();
                            }

                            if ($response->financial_status != "paid" && $response->cancelled_at !=  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();
                            }

                            if ($response->financial_status != "paid" && $response->cancelled_at ==  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();
                            }

                        }

                        if ($order['financial_status'] == 'paid') {

                            if ($response->financial_status == "paid" && $response->cancelled_at ==  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();

                                if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                    foreach ($order['line_items'] as $item) {

                                        $variant = Variant::where('product_id', $item['product_id'])->where('variant_id', $item['variant_id'])->first();

                                        if (count($variant) > 0) {
                                            $update = Variant::find($variant->id);
                                            $update->cantidad = $update->cantidad - $item['quantity'];
                                            $update->save();
                                        }

                                        $product = Product::find($item['product_id']);

                                        if (count($product) > 0) {
                                            $product->precio_unidad = $item['price'];
                                            $product->unidades_vendidas = $product->unidades_vendidas - $item['quantity'];
                                            $product->save();
                                        }
                                    }
                                }

                                $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                                if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                    $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                    if ($padre->state) {

                                        $find = Tercero::find($padre->id);
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos - 1;
                                        $find->total_price_orders = $find->total_price_orders - $order['total_price'];
                                        $find->ganacias = $find->total_price_orders * 0.05;
                                        $find->save();

                                        $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                        if (count($customer) > 0) {
                                            $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                            $metafields = json_decode($res->getBody(), true);
                                            $results = array();

                                            if (count($metafields['metafields']) > 0) {

                                                foreach ($metafields['metafields'] as $metafield) {
                                                    if ($metafield['key'] === 'referidos') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'referidos',
                                                                        'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'compras') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'compras',
                                                                        'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'valor') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'valor',
                                                                        'value' => '' . ($find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                        'value_type' => 'string'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }

                            if ($response->financial_status == "paid" && $response->cancelled_at !=  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();
                            }

                            if ($response->financial_status != "paid" && $response->cancelled_at !=  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();
                            }

                            if ($response->financial_status != "paid" && $response->cancelled_at ==  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();
                            }

                        }
                    }
                }

                if ($order['cancelled_at'] == null && $order['cancel_reason'] == null) {

                    if(count($response) == 0) {

                        $tipo_orden = '';
                        $i = 0;
                        $n = 0;

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) == 0) {
                                    LineItems::create([
                                        'id' => $item['id'],
                                        'variant_id' =>$item['variant_id'],
                                        'title' => $item['title'],
                                        'quantity' =>$item['quantity'],
                                        'price' => $item['price'],
                                        'grams' =>$item['grams'],
                                        'sku' => $item['sku'],
                                        'variant_title' =>$item['variant_title'],
                                        'vendor' => $item['vendor'],
                                        'fulfillment_service' =>$item['fulfillment_service'],
                                        'product_id' => $item['product_id'],
                                        'requires_shipping' =>$item['requires_shipping'],
                                        'taxable' => $item['taxable'],
                                        'gift_card' =>$item['gift_card'],
                                        'pre_tax_price' => $item['pre_tax_price'],
                                        'name' =>$item['name'],
                                        'variant_inventory_management' => $item['variant_inventory_management'],
                                        'properties' =>$item['properties'],
                                        'product_exists' => $item['product_exists'],
                                        'fulfillable_quantity' =>$item['fulfillable_quantity'],
                                        'total_discount' => $item['total_discount'],
                                        'fulfillment_status' =>$item['fulfillment_status'],
                                        'tax_lines' => $item['tax_lines'],
                                        'origin_location' =>$item['origin_location'],
                                        'destination_location' => (isset($item['destination_location'])) ? $item['destination_location'] : null,
                                        'order_name' => $order['name'],
                                        'date_order' =>$order['updated_at'],
                                    ]);
                                }

                                $product = Product::find($item['product_id']);

                                if (strtolower($item['vendor'])  == 'nacional' || strtolower($item['vendor'])  == 'a - nacional') {
                                    $n++;

                                    if (count($product) > 0) {
                                        $product->tipo_producto = 'nacional';
                                        $product->save();
                                    }
                                }
                                if (strtolower($item['vendor'])  != 'nacional' && strtolower($item['vendor'])  != 'a - nacional') {
                                    $i++;

                                    if (count($product) > 0) {
                                        $product->tipo_producto = 'internacional';
                                        $product->save();
                                    }
                                }
                            }
                        }

                        if ($i > 0 && $n > 0) {
                            $tipo_orden .= 'nacional/internacional';
                            $i = 0;
                            $n = 0;
                        }
                        if ($i > 0 && $n == 0) {
                            $tipo_orden .= 'internacional';
                            $i = 0;
                            $n = 0;
                        }
                        if ($i == 0 && $n > 0) {
                            $tipo_orden .= 'nacional';
                            $i = 0;
                            $n = 0;
                        }

                        Order::create([
                            'billing_address' => $order['billing_address'],
                            'browser_ip' => $order['browser_ip'],
                            'buyer_accepts_marketing' => $order['buyer_accepts_marketing'],
                            'cancel_reason' => $order['cancel_reason'],
                            'cancelled_at' => $order['cancelled_at'],
                            'cart_token' => $order['cart_token'],
                            'client_details' => $order['client_details'],
                            'closed_at' => $order['closed_at'],
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
                            'network_id' => 1,
                            'note' => $order['note'],
                            'note_attributes' => $order['note_attributes'],
                            'number' => $order['number'],
                            'order_id' => (int) $order['id'],
                            'order_number' => $order['order_number'],
                            'payment_details' => null,
                            'payment_gateway_names' => $order['payment_gateway_names'],
                            'phone' => $order['phone'],
                            'processed_at' => Carbon::parse($order['processed_at']),
                            'processing_method' => $order['processing_method'],
                            'referring_site' => $order['referring_site'],
                            'refunds' => $order['refunds'],
                            'shipping_address' => (!empty($order['shipping_address'])) ? $order['shipping_address'] : null,
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
                            'origin' => 'webhooks',
                            'tipo_orden' => $tipo_orden
                        ]);

                        $tipo_orden = '';

                        if ($order['financial_status'] == "paid") {

                            if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                foreach ($order['line_items'] as $item) {

                                    $line_item = LineItems::find($item['id']);

                                    if (count($line_item) == 0) {
                                        LineItems::create([
                                            'id' => $item['id'],
                                            'variant_id' =>$item['variant_id'],
                                            'title' => $item['title'],
                                            'quantity' =>$item['quantity'],
                                            'price' => $item['price'],
                                            'grams' =>$item['grams'],
                                            'sku' => $item['sku'],
                                            'variant_title' =>$item['variant_title'],
                                            'vendor' => $item['vendor'],
                                            'fulfillment_service' =>$item['fulfillment_service'],
                                            'product_id' => $item['product_id'],
                                            'requires_shipping' =>$item['requires_shipping'],
                                            'taxable' => $item['taxable'],
                                            'gift_card' =>$item['gift_card'],
                                            'pre_tax_price' => $item['pre_tax_price'],
                                            'name' =>$item['name'],
                                            'variant_inventory_management' => $item['variant_inventory_management'],
                                            'properties' =>$item['properties'],
                                            'product_exists' => $item['product_exists'],
                                            'fulfillable_quantity' =>$item['fulfillable_quantity'],
                                            'total_discount' => $item['total_discount'],
                                            'fulfillment_status' =>$item['fulfillment_status'],
                                            'tax_lines' => $item['tax_lines'],
                                            'origin_location' =>$item['origin_location'],
                                            'destination_location' => (isset($item['destination_location'])) ? $item['destination_location'] : null,
                                            'order_name' => $order['name'],
                                            'date_order' =>$order['updated_at'],
                                        ]);
                                    }


                                    if (count($line_item) > 0) {
                                        $line_item->date_order = $order['updated_at'];
                                        $line_item->save();
                                    }

                                    $variant = Variant::where('product_id', $item['product_id'])->where('variant_id', $item['variant_id'])->first();

                                    if (count($variant) == 0) {
                                        Variant::create([
                                            'product_id' => $item['product_id'],
                                            'variant_id' => $item['variant_id'],
                                            'cantidad' => $item['quantity'],
                                            'valor' => $item['price']
                                        ]);
                                    }

                                    if (count($variant) > 0) {
                                        $update = Variant::find($variant->id);
                                        $update->cantidad = $update->cantidad + $item['quantity'];
                                        $update->save();
                                    }

                                    $product = Product::find($item['product_id']);

                                    if (count($product) > 0) {
                                        $product->precio_unidad = $item['price'];
                                        $product->unidades_vendidas = $product->unidades_vendidas + $item['quantity'];
                                        $product->save();
                                    }

                                }
                            }

                            $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                            if (isset($tercero->networks) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if ($padre->state) {

                                    $find = Tercero::find($padre->id);
                                    $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                    $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                    $find->ganacias = $find->total_price_orders * 0.05;
                                    $find->save();

                                    $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                    if (count($customer) > 0) {

                                        $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                        $metafields = json_decode($res->getBody(), true);
                                        $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                        $x = explode('/', $headers[0]);
                                        $diferencia = $x[1] - $x[0];
                                        if ($diferencia < 10) {
                                            usleep(10000000);
                                        }
                                        $results = array();

                                        if (count($metafields['metafields']) > 0) {

                                            foreach ($metafields['metafields'] as $metafield) {

                                                if ($metafield['key'] === 'referidos') {
                                                    $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                            'form_params' => array(
                                                                'metafield' => array(
                                                                    'namespace' => 'customers',
                                                                    'key' => 'referidos',
                                                                    'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                    'value_type' => 'integer'
                                                                )
                                                            )
                                                        )
                                                    );
                                                    $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                                    $x = explode('/', $headers[0]);
                                                    $diferencia = $x[1] - $x[0];
                                                    if ($diferencia < 10) {
                                                        usleep(10000000);
                                                    }

                                                    array_push($results, json_decode($res->getBody(), true));
                                                }

                                                if ($metafield['key'] === 'compras') {
                                                    $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                            'form_params' => array(
                                                                'metafield' => array(
                                                                    'namespace' => 'customers',
                                                                    'key' => 'compras',
                                                                    'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                    'value_type' => 'integer'
                                                                )
                                                            )
                                                        )
                                                    );
                                                    $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                                    $x = explode('/', $headers[0]);
                                                    $diferencia = $x[1] - $x[0];
                                                    if ($diferencia < 10) {
                                                        usleep(10000000);
                                                    }

                                                    array_push($results, json_decode($res->getBody(), true));
                                                }

                                                if ($metafield['key'] === 'valor') {
                                                    $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                            'form_params' => array(
                                                                'metafield' => array(
                                                                    'namespace' => 'customers',
                                                                    'key' => 'valor',
                                                                    'value' => '' . ( $find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                    'value_type' => 'string'
                                                                )
                                                            )
                                                        )
                                                    );
                                                    $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                                    $x = explode('/', $headers[0]);
                                                    $diferencia = $x[1] - $x[0];
                                                    if ($diferencia < 10) {
                                                        usleep(10000000);
                                                    }

                                                    array_push($results, json_decode($res->getBody(), true));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {

                            if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                foreach ($order['line_items'] as $item) {

                                    $line_item = LineItems::find($item['id']);

                                    if (count($line_item) == 0) {
                                        LineItems::create([
                                            'id' => $item['id'],
                                            'variant_id' => $item['variant_id'],
                                            'title' => $item['title'],
                                            'quantity' => $item['quantity'],
                                            'price' => $item['price'],
                                            'grams' => $item['grams'],
                                            'sku' => $item['sku'],
                                            'variant_title' => $item['variant_title'],
                                            'vendor' => $item['vendor'],
                                            'fulfillment_service' => $item['fulfillment_service'],
                                            'product_id' => $item['product_id'],
                                            'requires_shipping' => $item['requires_shipping'],
                                            'taxable' => $item['taxable'],
                                            'gift_card' => $item['gift_card'],
                                            'pre_tax_price' => $item['pre_tax_price'],
                                            'name' => $item['name'],
                                            'variant_inventory_management' => $item['variant_inventory_management'],
                                            'properties' => $item['properties'],
                                            'product_exists' => $item['product_exists'],
                                            'fulfillable_quantity' => $item['fulfillable_quantity'],
                                            'total_discount' => $item['total_discount'],
                                            'fulfillment_status' => $item['fulfillment_status'],
                                            'tax_lines' => $item['tax_lines'],
                                            'origin_location' => $item['origin_location'],
                                            'destination_location' => (isset($item['destination_location'])) ? $item['destination_location'] : null,
                                            'order_name' => $order['name'],
                                            'date_order' => $order['updated_at'],
                                        ]);
                                    }


                                    if (count($line_item) > 0) {
                                        $line_item->date_order = $order['updated_at'];
                                        $line_item->save();
                                    }

                                }
                            }
                        }
                    }

                    if (count($response) > 0) {

                        if ($order['financial_status'] == 'paid') {

                            if ($response->financial_status != "paid" && $response->cancelled_at ==  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();


                                $log = Logorder::where('name', $update->name)
                                    ->where('checkout_id', $update->checkout_id)
                                    ->where('order_id', $update->order_id)
                                    ->first();

                                DB::table('logsorders')
                                    ->where('name', '=', $update->name)
                                    ->where('checkout_id', '=', $update->checkout_id)
                                    ->where('order_id', '=', $update->order_id)->delete();

                                if (count($log) > 0) {
                                    $log_delete = Logorder::find($log->id);
                                    if ($log_delete != null) {
                                        $log_delete->delete();
                                    }
                                }

                                if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                    foreach ($order['line_items'] as $item) {

                                        $variant = Variant::where('product_id', $item['product_id'])->where('variant_id', $item['variant_id'])->first();

                                        if (count($variant) == 0) {
                                            Variant::create([
                                                'product_id' => $item['product_id'],
                                                'variant_id' => $item['variant_id'],
                                                'cantidad' => $item['quantity'],
                                                'valor' => $item['price']
                                            ]);
                                        }

                                        if (count($variant) > 0) {
                                            $update = Variant::find($variant->id);
                                            $update->cantidad = $update->cantidad + $item['quantity'];
                                            $update->save();
                                        }

                                        $product = Product::find($item['product_id']);
                                        if (count($product) > 0) {
                                            $product->precio_unidad = $item['price'];
                                            $product->unidades_vendidas = $product->unidades_vendidas + $item['quantity'];
                                            $product->save();
                                        }
                                    }
                                }

                                $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                                if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                    $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                    if ($padre->state) {

                                        $find = Tercero::find($padre->id);
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                        $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                        $find->ganacias = $find->total_price_orders * 0.05;
                                        $find->save();
                                        $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                        if (count($customer) > 0) {
                                            $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                            $metafields = json_decode($res->getBody(), true);
                                            $results = array();

                                            if (count($metafields['metafields']) > 0) {

                                                foreach ($metafields['metafields'] as $metafield) {
                                                    if ($metafield['key'] === 'referidos') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'referidos',
                                                                        'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'compras') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'compras',
                                                                        'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'valor') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'valor',
                                                                        'value' => '' . ($find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                        'value_type' => 'string'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }


                            }

                            if ($response->financial_status != "paid" && $response->cancelled_at !=  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();


                                $log = Logorder::where('name', $update->name)
                                    ->where('checkout_id', $update->checkout_id)
                                    ->where('order_id', $update->order_id)
                                    ->first();

                                DB::table('logsorders')
                                    ->where('name', '=', $update->name)
                                    ->where('checkout_id', '=', $update->checkout_id)
                                    ->where('order_id', '=', $update->order_id)->delete();

                                if (count($log) > 0) {
                                    $log_delete = Logorder::find($log->id);
                                    if ($log_delete != null) {
                                        $log_delete->delete();
                                    }
                                }

                                if (isset($order['line_items']) && count($order['line_items']) > 0) {
                                    foreach ($order['line_items'] as $item) {

                                        $variant = Variant::where('product_id', $item['product_id'])->where('variant_id', $item['variant_id'])->first();

                                        if (count($variant) == 0) {
                                            Variant::create([
                                                'product_id' => $item['product_id'],
                                                'variant_id' => $item['variant_id'],
                                                'cantidad' => $item['quantity'],
                                                'valor' => $item['price']
                                            ]);
                                        }

                                        if (count($variant) > 0) {
                                            $update = Variant::find($variant->id);
                                            $update->cantidad = $update->cantidad + $item['quantity'];
                                            $update->save();
                                        }

                                        $product = Product::find($item['product_id']);
                                        if (count($product) > 0) {
                                            $product->precio_unidad = $item['price'];
                                            $product->unidades_vendidas = $product->unidades_vendidas + $item['quantity'];
                                            $product->save();
                                        }
                                    }
                                }

                                $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                                if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                    $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                    if ($padre->state) {

                                        $find = Tercero::find($padre->id);
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                        $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                        $find->ganacias = $find->total_price_orders * 0.05;
                                        $find->save();
                                        $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                        if (count($customer) > 0) {
                                            $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                            $metafields = json_decode($res->getBody(), true);
                                            $results = array();

                                            if (count($metafields['metafields']) > 0) {

                                                foreach ($metafields['metafields'] as $metafield) {
                                                    if ($metafield['key'] === 'referidos') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'referidos',
                                                                        'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'compras') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'compras',
                                                                        'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'valor') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'valor',
                                                                        'value' => '' . ($find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                        'value_type' => 'string'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }


                            }

                            if ($response->financial_status == "paid" && $response->cancelled_at !=  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();

                                if (isset($order['line_items']) && count($order['line_items']) > 0) {
                                    foreach ($order['line_items'] as $item) {

                                        $variant = Variant::where('product_id', $item['product_id'])->where('variant_id', $item['variant_id'])->first();

                                        if (count($variant) == 0) {
                                            Variant::create([
                                                'product_id' => $item['product_id'],
                                                'variant_id' => $item['variant_id'],
                                                'cantidad' => $item['quantity'],
                                                'valor' => $item['price']
                                            ]);
                                        }

                                        if (count($variant) > 0) {
                                            $update = Variant::find($variant->id);
                                            $update->cantidad = $update->cantidad + $item['quantity'];
                                            $update->save();
                                        }

                                        $product = Product::find($item['product_id']);
                                        if (count($product) > 0) {
                                            $product->precio_unidad = $item['price'];
                                            $product->unidades_vendidas = $product->unidades_vendidas + $item['quantity'];
                                            $product->save();
                                        }
                                    }
                                }

                                $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                                if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                    $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                    if ($padre->state) {

                                        $find = Tercero::find($padre->id);
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                        $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                        $find->ganacias = $find->total_price_orders * 0.05;
                                        $find->save();
                                        $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                        if (count($customer) > 0) {
                                            $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                            $metafields = json_decode($res->getBody(), true);
                                            $results = array();

                                            if (count($metafields['metafields']) > 0) {

                                                foreach ($metafields['metafields'] as $metafield) {
                                                    if ($metafield['key'] === 'referidos') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'referidos',
                                                                        'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'compras') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'compras',
                                                                        'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'valor') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'valor',
                                                                        'value' => '' . ($find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                        'value_type' => 'string'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }

                            if ($response->financial_status == "paid" && $response->cancelled_at ==  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();

                                if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                    foreach ($order['line_items'] as $item) {

                                        $variant = Variant::where('product_id', $item['product_id'])->where('variant_id', $item['variant_id'])->first();

                                        if (count($variant) == 0) {
                                            Variant::create([
                                                'product_id' => $item['product_id'],
                                                'variant_id' => $item['variant_id'],
                                                'cantidad' => $item['quantity'],
                                                'valor' => $item['price']
                                            ]);
                                        }

                                    }
                                }

                            }
                        }

                        if ($order['financial_status'] != 'paid') {

                            if ($response->financial_status == "paid" && $response->cancelled_at ==  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();

                                if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                    foreach ($order['line_items'] as $item) {

                                        $variant = Variant::where('product_id', $item['product_id'])->where('variant_id', $item['variant_id'])->first();

                                        if (count($variant) > 0) {
                                            $update = Variant::find($variant->id);
                                            $update->cantidad = $update->cantidad - $item['quantity'];
                                            $update->save();
                                        }

                                        $product = Product::find($item['product_id']);

                                        if (count($product) > 0) {
                                            $product->precio_unidad = $item['price'];
                                            $product->unidades_vendidas = $product->unidades_vendidas - $item['quantity'];
                                            $product->save();
                                        }
                                    }
                                }

                                $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                                if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                    $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                    if ($padre->state) {

                                        $find = Tercero::find($padre->id);
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos - 1;
                                        $find->total_price_orders = $find->total_price_orders - $order['total_price'];
                                        $find->ganacias = $find->total_price_orders * 0.05;
                                        $find->save();

                                        $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                        if (count($customer) > 0) {
                                            $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                            $metafields = json_decode($res->getBody(), true);
                                            $results = array();

                                            if (count($metafields['metafields']) > 0) {

                                                foreach ($metafields['metafields'] as $metafield) {
                                                    if ($metafield['key'] === 'referidos') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'referidos',
                                                                        'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'compras') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'compras',
                                                                        'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }

                                                    if ($metafield['key'] === 'valor') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'valor',
                                                                        'value' => '' . ($find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                        'value_type' => 'string'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                        array_push($results, json_decode($res->getBody(), true));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }

                            if ($response->financial_status == "paid" && $response->cancelled_at !=  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();

                            }

                            if ($response->financial_status != "paid" && $response->cancelled_at !=  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();

                            }

                            if ($response->financial_status != "paid" && $response->cancelled_at ==  null) {

                                $update = Order::find($response->id);
                                $update->closed_at = $order['closed_at'];
                                $update->cancelled_at = $order['cancelled_at'];
                                $update->cancel_reason = $order['cancel_reason'];
                                $update->financial_status = $order['financial_status'];
                                $update->updated_at = Carbon::parse($order['updated_at']);
                                $update->save();

                            }

                        }
                    }
                }
            }
        }

        $this->info('Las ordenes han sido descargados correctamente');
    }
}
