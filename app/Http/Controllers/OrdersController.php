<?php
namespace App\Http\Controllers;
use App\Entities\Tercero_network;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Order;
use Carbon\Carbon;
use App\Product;
use App\Entities\Tercero;
use App\Customer;
use DB;
use App\Logorder;
use Yajra\Datatables\Datatables;
use MP;
use MercadoPagoException;
use Bican\Roles\Models\Role;
use Bican\Roles\Models\Permission;
use Auth;
use App\Variant;
use App\LineItems;

class OrdersController extends Controller
{
    public function listpaid()
    {
        return view('admin.orders.paid');
    }
    public function listpending()
    {
        return view('admin.orders.pending');
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
    public function paid()
    {

        ini_set('memory_limit','1000M');
        ini_set('xdebug.max_nesting_level', 120);
        ini_set('max_execution_time', 3000);
        $orders = Order::where('financial_status', 'paid')->where('cancelled_at', null)->get();
        $result = array();
        foreach ($orders as $order) {
            foreach ($order->line_items as $item) {
                if (isset($order->shipping_lines[0]['price'])){
                    $data = [
                        'nombre_producto' => $item['name'],
                        'numero_orden' => $order->name,
                        'precio_unidad' => number_format($item['price']),
                        'cantidad' => number_format($item['quantity']),
                        'costo_envio' => number_format($order->shipping_lines[0]['price']),
                        'fecha_compra_cliente' => $order->created_at,
                        'total' => number_format($order->total_price)
                    ];
                } else {
                    $data = [
                        'nombre_producto' => $item['name'],
                        'numero_orden' => $order->name,
                        'precio_unidad' => number_format($item['price']),
                        'cantidad' => number_format($item['quantity']),
                        'costo_envio' => number_format(0),
                        'fecha_compra_cliente' => $order->created_at,
                        'total' => number_format($order->total_price)
                    ];
                }
                array_push($result, $data);
            }
        }
        $send = collect($result);
        return Datatables::of($send)
            ->addColumn('nombre_producto', function ($send) {
                return '<div align=left>' . $send['nombre_producto'] . '</div>';
            })
            ->addColumn('numero_orden', function ($send) {
                return '<div align=left>' . $send['numero_orden'] . '</div>';
            })
            ->addColumn('precio_unidad', function ($send) {
                return '<div align=left>' . $send['precio_unidad'] . '</div>';
            })
            ->addColumn('cantidad', function ($send) {
                return '<div align=left>' . $send['cantidad'] . '</div>';
            })
            ->addColumn('costo_envio', function ($send) {
                return '<div align=left>' . $send['costo_envio'] . '</div>';
            })
            ->addColumn('fecha_compra_cliente', function ($send) {
                return '<div align=left>' . Carbon::parse($send['fecha_compra_cliente'])->toFormattedDateString() . '</div>';
            })
            ->addColumn('total', function ($send) {
                return '<div align=left>' . $send['total'] . '</div>';
            })
            ->make(true);

    }
    public function pending()
    {
        ini_set('memory_limit','1000M');
        ini_set('xdebug.max_nesting_level', 120);
        ini_set('max_execution_time', 3000);
        $orders = Order::where('financial_status', 'pending')->where('cancelled_at', null)->get();
        $result = array();
        foreach ($orders as $order) {
            foreach ($order->line_items as $item) {
                if (isset($order->shipping_lines[0]['price'])){
                    $data = [
                        'nombre_producto' => $item['name'],
                        'numero_orden' => $order->name,
                        'precio_unidad' => number_format($item['price']),
                        'cantidad' => number_format($item['quantity']),
                        'costo_envio' => number_format($order->shipping_lines[0]['price']),
                        'fecha_compra_cliente' => $order->created_at,
                        'total' => number_format($order->total_price)
                    ];
                } else {
                    $data = [
                        'nombre_producto' => $item['name'],
                        'numero_orden' => $order->name,
                        'precio_unidad' => number_format($item['price']),
                        'cantidad' => number_format($item['quantity']),
                        'costo_envio' => number_format(0),
                        'fecha_compra_cliente' => $order->created_at,
                        'total' => number_format($order->total_price)
                    ];
                }
                array_push($result, $data);
            }
        }
        $send = collect($result);
        return Datatables::of($send)
            ->addColumn('nombre_producto', function ($send) {
                return '<div align=left>' . $send['nombre_producto'] . '</div>';
            })
            ->addColumn('numero_orden', function ($send) {
                return '<div align=left>' . $send['numero_orden'] . '</div>';
            })
            ->addColumn('precio_unidad', function ($send) {
                return '<div align=left>' . $send['precio_unidad'] . '</div>';
            })
            ->addColumn('cantidad', function ($send) {
                return '<div align=left>' . $send['cantidad'] . '</div>';
            })
            ->addColumn('costo_envio', function ($send) {
                return '<div align=left>' . $send['costo_envio'] . '</div>';
            })
            ->addColumn('fecha_compra_cliente', function ($send) {
                return '<div align=left>' . Carbon::parse($send['fecha_compra_cliente'])->toFormattedDateString() . '</div>';
            })
            ->addColumn('total', function ($send) {
                return '<div align=left>' . $send['total'] . '</div>';
            })
            ->make(true);
    }
    public function home()
    {
        return view('admin.orders.home');
    }
    public function edit($id)
    {
        $order = Order::find($id);
        return view('admin.orders.edit')->with([
            'order' => $order
        ]);
    }
    public function up(Request $request, $id)
    {
        if (isset($request['date']) && isset($request['tipo_orden']) && !isset($request['code']) && !isset($request['url']) && !isset($request['code_internacional'])) {

            $date = $request['date'];
            $order = Order::find($id);
            $order->tipo_orden = $request['tipo_orden'];
            $order->fecha_compra = Carbon::parse($date);
            $order->estado_orden = 'comprado';
            $order->bitacora = currentUser();
            $order->save();

            if ($order) {
                $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                $client = new \GuzzleHttp\Client();
                $res = $client->request('get', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments.json');
                $fulfillments = json_decode($res->getBody(), true);

                if ($res->getStatusCode() == 200) {

                    if (count($fulfillments['fulfillments']) > 0) {
                        $ok = null;
                        foreach ($fulfillments['fulfillments'] as $fulfillment) {

                            if ($fulfillment['status'] == 'success') {
                                $ok = $fulfillment['id'] ;
                            }
                        }

                        if ($ok != null) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $ok .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "confirmed"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Compra realizada con exito.']);
                            }


                        } else {

                            $res = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments.json', array(
                                'form_params' => array(
                                    'fulfillment' => array(
                                        "order_id" => $order->order_id
                                    )
                                )
                            ));

                            $create = json_decode($res->getBody(), true);

                            if ($res->getStatusCode() == 201) {

                                $event = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $create['fulfillment']['id'] .'/events.json', array(
                                        'form_params' => array(
                                            'event' => array(
                                                "status" => "confirmed"
                                            )
                                        )
                                    )
                                );

                                if ($event->getStatusCode() == 201) {
                                    return redirect()->back()->with(['success' =>'Compra realizada con exito.']);
                                }
                            }
                        }

                    } else {
                        $res = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments.json', array(
                            'form_params' => array(
                                'fulfillment' => array(
                                    "order_id" => $order->order_id
                                )
                            )
                        ));
                        $create = json_decode($res->getBody(), true);

                        if ($res->getStatusCode() == 201) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $create['fulfillment']['id'] .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "confirmed"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Compra realizada con exito.']);
                            }
                        }
                    }
                }

            }
        }

        if (!isset($request['date']) && !isset($request['tipo_orden']) && isset($request['code']) && !isset($request['url'])) {

            $code = $request['code'];
            $order = Order::find($id);
            $order->codigo_envio = $code;
            $order->fecha_envio_n = Carbon::now();
            $order->estado_orden = 'envio_nacional';
            $order->bitacora = currentUser();
            $order->save();


            if ($order && $order->tipo_orden == 'nacional') {

                $update = Order::find($order->id);
                $update->url_envio = 'http://www.enviacolvanes.com.co/Contenido.aspx?rastreo=' . $code;
                $update->save();

                $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                $client = new \GuzzleHttp\Client();

                $res = $client->request('get', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments.json');
                $fulfillments = json_decode($res->getBody(), true);

                if ($res->getStatusCode() == 200) {
                    if (isset($fulfillments['fulfillments'][0]) && count($fulfillments['fulfillments'][0]) > 0 && $fulfillments['fulfillments'][0]['status'] == 'success') {

                        $fulfillment = $client->request('put', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'.json', array(
                                'form_params' => array(
                                    'fulfillment' => array(
                                        "tracking_company" => "Envia",
                                        "tracking_number" => $update->codigo_envio,
                                        "tracking_url" => $update->url_envio
                                    )
                                )
                            )
                        );

                        if ($fulfillment->getStatusCode() == 200) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "out_for_delivery"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Código Nacional agregado con exito.']);
                            }
                        }
                    }
                }
            }

            if ($order && $order->tipo_orden != 'nacional') {

                $update = Order::find($order->id);
                $update->url_envio = 'http://www.enviacolvanes.com.co/Contenido.aspx?rastreo=' . $code;
                $update->save();

                $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                $client = new \GuzzleHttp\Client();

                $res = $client->request('get', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments.json');
                $fulfillments = json_decode($res->getBody(), true);

                if ($res->getStatusCode() == 200) {
                    if (isset($fulfillments['fulfillments'][0]) && count($fulfillments['fulfillments'][0]) > 0 && $fulfillments['fulfillments'][0]['status'] == 'success') {

                        $fulfillment = $client->request('put', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'.json', array(
                                'form_params' => array(
                                    'fulfillment' => array(
                                        "tracking_company" => "Envia",
                                        "tracking_number" => $update->codigo_envio,
                                        "tracking_url" => $update->url_envio
                                    )
                                )
                            )
                        );

                        if ($fulfillment->getStatusCode() == 200) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $update->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "out_for_delivery"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Código Nacional agregado con exito.']);
                            }
                        }
                    }
                }
            }
        }

        if (!isset($request['date']) && !isset($request['tipo_orden']) && !isset($request['code']) && isset($request['code_internacional']) && isset($request['url'])) {
            $code = $request['code_internacional'];
            $order = Order::find($id);
            $order->codigo_envio_internacional = $code;
            $order->url_envio = $request['url'];
            $order->fecha_envio_i = Carbon::now();
            $order->estado_orden = 'envio_internacional';
            $order->bitacora = currentUser();
            $order->save();

            if ($order && ($order->tipo_orden == 'internacional' || $order->tipo_orden == 'nacional/internacional')) {
                $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
                $client = new \GuzzleHttp\Client();

                $res = $client->request('get', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments.json');
                $fulfillments = json_decode($res->getBody(), true);

                if ($res->getStatusCode() == 200) {
                    if (isset($fulfillments['fulfillments'][0]) && count($fulfillments['fulfillments'][0]) > 0 && $fulfillments['fulfillments'][0]['status'] == 'success') {

                        $fulfillment = $client->request('put', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'.json', array(
                                'form_params' => array(
                                    'fulfillment' => array(
                                        "tracking_number" => $order->codigo_envio_internacional,
                                        "tracking_url" => $order->url_envio
                                    )
                                )
                            )
                        );

                        if ($fulfillment->getStatusCode() == 200) {

                            $event = $client->request('post', $api_url . '/admin/orders/'. $order->order_id .'/fulfillments/'. $fulfillments['fulfillments'][0]['id'] .'/events.json', array(
                                    'form_params' => array(
                                        'event' => array(
                                            "status" => "in_transit"
                                        )
                                    )
                                )
                            );

                            if ($event->getStatusCode() == 201) {
                                return redirect()->back()->with(['success' =>'Código Internacional agregado con exito.']);
                            }
                        }
                    }
                }

            }
        }
    }
    public function anyData()
    {
        ini_set('memory_limit','2000M');

        if (Auth::user()->hasRole('logistica') && !Auth::user()->hasRole('administrador')) {
            $orders = Order::where('financial_status', 'paid')
                ->where('cancelled_at', null)
                ->get();
            $send = collect($orders);
            return Datatables::of($send )
                ->addColumn('order', function ($send) {
                    $result = '';
                    foreach ($send->line_items as $item ){
                        $product = Product::find($item['product_id']);
                        if(count($product['image']) > 0 && count($product['images']) > 0) {
                            $result .= '<div class="container" style="width: 100%">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Nombre: ' . $item['name'] . '</strong></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <!-- Left-aligned media object -->
                                                        <div class="media">
                                                            <div class="media-left">
                                                                <img src="' . $product['image']['src'] . '" class="media-object" style="width:60px">
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading">Precio unidad: ' . number_format($item['price']) . '</h4>
                                                                <p>Cantidad: ' . $item['quantity'] . '</p>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <hr>';
                        }
                    }
                    if (count($send->shipping_lines) > 0) {
                        foreach ($send->shipping_lines as $line) {
                            return '
                  
                            <div class="text-left">
                                <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">'. $send->order_number .'</button>
                                <!-- Modal -->
                                <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                            </div>
                                            <div class="modal-body">
                                                   '.$result.'
                                                   <p>Costo Envio: '.number_format($line['price']) .'</p>
                                                   <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                   
                        ';
                        }
                    } else {
                        return '
                  
                    <div class="text-left">
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">'. $send->order_number .'</button>
                        <!-- Modal -->
                        <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                    </div>
                                    <div class="modal-body">
                                       
                                           '.$result.'
                                           <p>Costo Envio:  0</p>
                                           <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                        
                ';
                    }
                })
                ->addColumn('customer', function ($send) {
                    $customer = Customer::where('email', $send->email)->first();
                    $orden_sin = 'Orden sin cliente';
                    if (count($customer) > 0) {
                        return '<div align=left>' . $customer->first_name . '</div>';
                    } else {
                        return '<div align=left>'. $orden_sin .'</div>';
                    }
                })
                ->addColumn('email', function ($send) {
                    return '<div align=left>'. $send->email .'</div>';
                })
                ->addColumn('address', function ($send) {
                    return '<div align=left>'. $send->billing_address['address1'] . ', ' . $send->billing_address['city'] . ', ' .$send->billing_address['country'] . '</div>';
                })
                ->addColumn('phone', function ($send) {
                    $phone = str_replace(' ', '', $send->billing_address['phone']);
                    return '<div align=left>'. $phone .'</div>';
                })
                ->addColumn('value', function ($send) {
                    return '<div align=left>' . number_format($send->total_price) . '</div>';
                })
                ->addColumn('name', function ($send) {
                    return '<div align=left>'. $send->name . '</div>';
                })

                ->addColumn('financial_status', function ($send) {
                    return '<div align=left>' . $send->financial_status. '</div>';
                })
                ->addColumn('fecha_compra_cliente', function ($send) {
                    return '<div align=left>' . Carbon::parse($send->created_at)->toFormattedDateString() . '</div>';
                })
                ->addColumn('fecha_compra', function ($send) {
                    return '<div align=left>' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</div>';
                })
                ->addColumn('tipo_orden', function ($send) {
                    return '<div align=left>' . $send->tipo_orden . '</div>';
                })
                ->addColumn('codigo_envio', function ($send) {
                    return '<div align=left>' . $send->codigo_envio . '</div>';
                })
                ->addColumn('codigo_envio_internacional', function ($send) {
                    return '<div align=left>' . $send->codigo_envio_internacional . '</div>';
                })
                ->addColumn('estado_orden', function ($send) {

                    if ( $send->tipo_orden == 'internacional' || $send->tipo_orden == 'nacional/internacional' ) {
                        $result = '';
                        $state = '';
                        if ($send->estado_orden == "pendiente") {
                            $state .= 'Pendiente';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "comprado") {
                            $state .= 'Comprado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_internacional") {
                            $state .= 'Internacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle" >3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_nacional") {
                            $state .= 'Nacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                      <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "entregado") {
                            $state .= 'Entregado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle" >5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Entrega: ' . Carbon::parse($send->fecha_entrega)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        return '
                        <div align=left>
                            <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->order_number .'">' . $state . '</button>
                            <!-- Modal -->
                            <div id="myModal-'. $send->order_number .'" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$send->tipo_orden .' #'. $send->order_number .'</h4>
                                        </div>
                                        <div class="modal-body">
                                            '.$result.'
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       ';
                    }
                    if ($send->tipo_orden == 'nacional') {
                        $result = '';
                        $state = '';
                        if ($send->estado_orden == "pendiente") {
                            $state .= 'Pendiente';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "comprado") {
                            $state .= 'Comprado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_nacional") {
                            $state .= 'Nacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "entregado") {
                            $state .= 'Entregado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle" >4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Entrega: ' . Carbon::parse($send->fecha_entrega)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        return '
                        <div align=left>
                            <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->order_number .'">' . $state . '</button>
                            <!-- Modal -->
                            <div id="myModal-'. $send->order_number .'" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$send->tipo_orden .' #'. $send->order_number .'</h4>
                                        </div>
                                        <div class="modal-body">
                                            '.$result.'
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       ';
                    }

                })
                ->make(true);
        }
        else {
            $orders = Order::where('financial_status', 'paid')
                ->where('cancelled_at', null)
                ->get();
            $send = collect($orders);
            return Datatables::of($send )
                ->addColumn('order', function ($send) {
                    $result = '';
                    foreach ($send->line_items as $item ){
                        $product = Product::find($item['product_id']);
                        if(count($product['image']) > 0 && count($product['images']) > 0) {
                            $result .= '<div class="container" style="width: 100%">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Nombre: ' . $item['name'] . '</strong></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <!-- Left-aligned media object -->
                                                        <div class="media">
                                                            <div class="media-left">
                                                                <img src="' . $product['image']['src'] . '" class="media-object" style="width:60px">
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading">Precio unidad: ' . number_format($item['price']) . '</h4>
                                                                <p>Cantidad: ' . $item['quantity'] . '</p>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <hr>';
                        }
                    }
                    if (count($send->shipping_lines) > 0) {
                        foreach ($send->shipping_lines as $line) {
                            return '
                  
                            <div class="text-left">
                                <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">Ver</button>
                                <!-- Modal -->
                                <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                            </div>
                                            <div class="modal-body">
                                                   '.$result.'
                                                   <p>Costo Envio: '.number_format($line['price']) .'</p>
                                                   <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                   
                        ';
                        }
                    } else {
                        return '
                  
                    <div class="text-left">
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">Ver</button>
                        <!-- Modal -->
                        <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                    </div>
                                    <div class="modal-body">
                                       
                                           '.$result.'
                                           <p>Costo Envio:  0</p>
                                           <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                        
                ';
                    }
                })
                ->addColumn('customer', function ($send) {
                    $customer = Customer::where('email', $send->email)->first();
                    $orden_sin = 'Orden sin cliente';
                    if (count($customer) > 0) {
                        return '<div align=left>' . $customer->first_name . '</div>';
                    } else {
                        return '<div align=left>'. $orden_sin .'</div>';
                    }
                })
                ->addColumn('email', function ($send) {
                    return '<div align=left>'. $send->email .'</div>';
                })
                ->addColumn('address', function ($send) {
                    return '<div align=left>'. $send->billing_address['address1'] . ', ' . $send->billing_address['city'] . ', ' .$send->billing_address['country'] . '</div>';
                })
                ->addColumn('phone', function ($send) {
                    $phone = str_replace(' ', '', $send->billing_address['phone']);
                    return '<div align=left>'. $phone .'</div>';
                })
                ->addColumn('value', function ($send) {
                    return '<div align=left>' . number_format($send->total_price) . '</div>';
                })
                ->addColumn('name', function ($send) {
                    return '<div align=left>'. $send->name . '</div>';
                })
                ->addColumn('order', function ($send) {
                    $result = '';
                    foreach ($send->line_items as $item ){
                        $product = Product::find($item['product_id']);
                        if(count($product['image']) > 0 && count($product['images']) > 0) {
                            $result .= '<div class="container" style="width: 100%">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Nombre: ' . $item['name'] . '</strong></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <!-- Left-aligned media object -->
                                                        <div class="media">
                                                            <div class="media-left">
                                                                <img src="' . $product['image']['src'] . '" class="media-object" style="width:60px">
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading">Precio unidad: ' . number_format($item['price']) . '</h4>
                                                                <p>Cantidad: ' . $item['quantity'] . '</p>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <hr>';
                        }
                    }
                    if (count($send->shipping_lines) > 0) {
                        foreach ($send->shipping_lines as $line) {
                            return '
                  
                            <div class="text-left">
                                <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">Ver</button>
                                <!-- Modal -->
                                <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                            </div>
                                            <div class="modal-body">
                                                   '.$result.'
                                                   <p>Costo Envio: '.number_format($line['price']) .'</p>
                                                   <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                   
                        ';
                        }
                    } else {
                        return '
                  
                    <div class="text-left">
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">Ver</button>
                        <!-- Modal -->
                        <div id="myModal'. $send->order_number .'" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" style="color: #f60620">#'. $send->order_number .'</h4>
                                    </div>
                                    <div class="modal-body">
                                       
                                           '.$result.'
                                           <p>Costo Envio:  0</p>
                                           <h4 class="media-heading">Precio Total: ' . number_format($send->total_price) . '</h4>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                        
                ';
                    }
                })
                ->addColumn('financial_status', function ($send) {
                    return '<div align=left>' . $send->financial_status. '</div>';
                })
                ->addColumn('fecha_compra_cliente', function ($send) {
                    return '<div align=left>' . Carbon::parse($send->created_at)->toFormattedDateString() . '</div>';
                })
                ->addColumn('fecha_compra', function ($send) {
                    return '<div align=left>' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</div>';
                })
                ->addColumn('tipo_orden', function ($send) {
                    return '<div align=left>' . $send->tipo_orden . '</div>';
                })
                ->addColumn('codigo_envio', function ($send) {
                    return '<div align=left>' . $send->codigo_envio . '</div>';
                })
                ->addColumn('codigo_envio_internacional', function ($send) {
                    return '<div align=left>' . $send->codigo_envio_internacional . '</div>';
                })
                ->addColumn('estado_orden', function ($send) {

                    if ( $send->tipo_orden == 'internacional' || $send->tipo_orden == 'nacional/internacional' ) {
                        $result = '';
                        $state = '';
                        if ($send->estado_orden == "pendiente") {
                            $state .= 'Pendiente';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "comprado") {
                            $state .= 'Comprado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_internacional") {
                            $state .= 'Internacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle" >3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_nacional") {
                            $state .= 'Nacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                      <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "entregado") {
                            $state .= 'Entregado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Internacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle" >5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Internacional: ' . Carbon::parse($send->fecha_envio_i)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Entrega: ' . Carbon::parse($send->fecha_entrega)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        return '
                        <div align=left>
                            <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->order_number .'">' . $state . '</button>
                            <!-- Modal -->
                            <div id="myModal-'. $send->order_number .'" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$send->tipo_orden .' #'. $send->order_number .'</h4>
                                        </div>
                                        <div class="modal-body">
                                            '.$result.'
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       ';
                    }
                    if ($send->tipo_orden == 'nacional') {
                        $result = '';
                        $state = '';
                        if ($send->estado_orden == "pendiente") {
                            $state .= 'Pendiente';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "comprado") {
                            $state .= 'Comprado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "envio_nacional") {
                            $state .= 'Nacional';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-primary btn-circle">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        if ($send->estado_orden == "entregado") {
                            $state .= 'Entregado';
                            $result .= '        <div class="stepwizard">
                                                <div class="stepwizard-row setup-panel">
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">1</a>
                                                        <p>Pendiente</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                                        <p>Comprado</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                                                        <p>Envio Nacional</p>
                                                    </div>
                                                    <div class="stepwizard-step">
                                                        <a  type="button" class="btn btn-primary btn-circle" >4</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Orden: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Envio Nacional: ' . Carbon::parse($send->fecha_envio_n)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha de Entrega: ' . Carbon::parse($send->fecha_entrega)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            ';
                        }
                        return '
                        <div align=left>
                            <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->order_number .'">' . $state . '</button>
                            <!-- Modal -->
                            <div id="myModal-'. $send->order_number .'" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$send->tipo_orden .' #'. $send->order_number .'</h4>
                                        </div>
                                        <div class="modal-body">
                                            '.$result.'
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       ';
                    }

                })
                ->addColumn('action', function ($send ) {
                    if ($send->fecha_compra == null) {
                        return '<div align=left><a href="/admin/orders/'. $send->id .'/edit"  class="btn btn-danger btn-xs text-center" style="width: 100%">Comprar</a></div>';
                    } else {
                        return '<div align=left><a href="/admin/orders/'. $send->id .'/edit"  class="btn btn-danger btn-xs text-center" style="width: 100%">Envio</a></div>';
                    }

                })
                ->make(true);
        }

    }
    public function index()
    {
        return view('admin.orders.index');
    }
    public function orders()
    {
        $orders = Logorder::all();
        $send = collect($orders);
        return Datatables::of($send )
            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send->id . '</div>';
            })
            ->addColumn('order_id', function ($send) {
                return '<div align=left>' . $send->name . '</div>';
            })
            ->addColumn('checkout_id', function ($send) {
                return '<div align=left>' . $send->checkout_id. '</div>';
            })
            ->addColumn('value', function ($send) {
                return '<div align=left>' . number_format($send->value) . '</div>';
            })
            ->addColumn('status_shopify', function ($send) {
                return '<div align=left>' . $send->status_shopify . '</div>';
            })
            ->addColumn('status_mercadopago', function ($send) {
                return '<div align=left>' . $send->status_mercadopago . '</div>';
            })
            ->addColumn('payment_method_id', function ($send) {
                return '<div align=left>' . $send->payment_method_id . '</div>';
            })
            ->make(true);
    }
    public function status_orders()
    {
        $orders = DB::table('logsorders')
            ->select(DB::raw('count(id) as number'), 'status_shopify', 'status_mercadopago', 'payment_method_id')
            ->groupBy('status_shopify', 'status_mercadopago', 'payment_method_id')
            ->get();
        $send = collect($orders);
        return Datatables::of($send )
            ->addColumn('number', function ($send) {
                return '<div align=left>' . $send->number . '</div>';
            })
            ->addColumn('status_shopify', function ($send) {
                return '<div align=left>' . $send->status_shopify . '</div>';
            })
            ->addColumn('status_mercadopago', function ($send) {
                return '<div align=left>' . $send->status_mercadopago. '</div>';
            })
            ->addColumn('payment_method_id', function ($send) {
                return '<div align=left>' . $send->payment_method_id . '</div>';
            })
            ->make(true);
    }
    public function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, 'afc86df7e11dcbe0ab414fa158ac1767', true));
        return hash_equals($hmac_header, $calculated_hmac);
    }
    public function create()
    {
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $input = file_get_contents('php://input');
        $order = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($order), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {

            $response = Order::where('network_id', 1)
                ->where('name', $order['name'])
                ->where('order_id', $order['id'])
                ->get();

            if ($order['cancelled_at'] != null) {

                if(count($response) == 0) {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;

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
                        'cancelled_at' => Carbon::parse($order['cancelled_at']),
                        'cart_token' => $order['cart_token'],
                        'client_details' => $order['client_details'],
                        'closed_at' => Carbon::parse($order['closed_at']),
                        'currency' => $order['currency'],
                        'customer_id' => $order['id'],
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
                        'origin' => 'crons',
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
                    return response()->json(['status' => 'order processed'], 200);


                } else {
                    return response()->json(['status' => 'order not processed'], 200);
                }
            }

            if ($order['cancelled_at'] == null) {
                $send = array();
                if(count($response) == 0) {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;

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
                        'closed_at' => Carbon::parse($order['closed_at']),
                        'currency' => $order['currency'],
                        'customer_id' => $order['id'],
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
                        'origin' => 'crons',
                        'tipo_orden' => $tipo_orden
                    ]);

                    $tipo_orden = '';

                    $customer = Customer::where('email', $order['email'])->first();

                    if ($order['financial_status'] == "paid") {

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                if ($item['product_id'] == 10332997761 && count($customer) > 0) {

                                    $send = [
                                        'form_params' => [
                                            'gift_card' => [
                                                "note" => "Por la compra del producto:  " . $item['title'] . " has recibido este bono. Sigue comprando y ganando con diahello.",
                                                "initial_value" => 1 * $item['quantity'],
                                                "template_suffix" => "gift_cards.birthday.liquid",
                                                "currency" => "COP",
                                                "customer_id" => $customer->customer_id,
                                            ]
                                        ]
                                    ];

                                    //$res = $client->request('post', $api_url . '/admin/gift_cards.json', $send);
                                    //$result = json_decode($res->getBody(), true);

                                    //return response()->json(['status' => 'giff_card created', 'gift_card' => $result], 200);
                                }

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

                        if (count($tercero) > 0) {

                            if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if (count($padre) > 0) {

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

                                        return response()->json(['status' => 'The resource is created successfully', $order['customer']], 200);
                                    }

                                } else {

                                    return response()->json(['status' => 'The father was not found', $order['customer']], 200);
                                }

                            }

                        } else {

                            $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                            if (count($padre) > 0) {

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

                                    return response()->json(['status' => 'The resource is created successfully', $send], 200);
                                }

                            } else {

                                $find = Tercero::find(26);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                return response()->json(['status' => 'The resource is created successfully', $send], 200);
                            }

                        }
                    }

                    return response()->json(['status' => 'order not processed'], 200);

                } else {

                    return response()->json(['status' => 'order not processed'], 200);
                }
            }
        }
    }
    public function update()
    {
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $input = file_get_contents('php://input');
        $order = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($order), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {

            $result = Order::where('order_id', $order['id'])
                ->where('email', $order['email'])
                ->where('network_id', 1)
                ->first();

            if ($order['cancelled_at'] != null && $order['financial_status'] != 'paid') {

                if (count($result) > 0) {

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
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

                        if (count($tercero) > 0) {

                            if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if (count($padre) > 0) {

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

                                        return response()->json(['status' => 'The resource is created successfully'], 200);
                                    }

                                } else {

                                    return response()->json(['status' => 'The father was not found'], 200);
                                }

                            }

                        } else {

                            $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                            if (count($padre) > 0) {

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

                                    return response()->json(['status' => 'The resource is created successfully'], 200);
                                }

                            } else {

                                $find = Tercero::find(26);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos - 1;
                                $find->total_price_orders = $find->total_price_orders - $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                return response()->json(['status' => 'The resource is created successfully'], 200);
                            }

                        }

                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    return response()->json(['status' => 'order not processed'], 200);

                } else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {
                        foreach ($order['line_items'] as $item) {
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
                        'cancelled_at' =>$order['cancelled_at'],
                        'cart_token' => $order['cart_token'],
                        'client_details' => $order['client_details'],
                        'closed_at' => $order['closed_at'],
                        'currency' => $order['currency'],
                        'customer_id' => $order['id'],
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

                    $customer = Customer::where('email', $order['email'])->first();

                    foreach ($order['line_items'] as $item) {

                        if ($item['product_id'] == 10332997761 && count($customer) > 0) {

                            $send = [
                                'form_params' => [
                                    'gift_card' => [
                                        "note" => "Por la compra del producto:  " . $item['title'] . " has recibido este bono. Sigue comprando y ganando con diahello.",
                                        "initial_value" => 1 * $item['quantity'],
                                        "template_suffix" => "gift_cards.birthday.liquid",
                                        "currency" => "COP",
                                        "customer_id" => $customer->customer_id,
                                    ]
                                ]
                            ];

                            //$res = $client->request('post', $api_url . '/admin/gift_cards.json', $send);
                            //$result = json_decode($res->getBody(), true);

                            //return response()->json(['status' => 'giff_card created', 'gift_card' => $result], 200);
                        }

                    }

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            }

            if ($order['cancelled_at'] != null && $order['financial_status'] == 'paid') {

                if (count($result) > 0) {

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
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

                        if (count($tercero) > 0) {

                            if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if (count($padre) > 0) {

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

                                        return response()->json(['status' => 'The resource is created successfully'], 200);
                                    }

                                } else {

                                    return response()->json(['status' => 'The father was not found'], 200);
                                }

                            }

                        } else {

                            $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                            if (count($padre) > 0) {

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

                                    return response()->json(['status' => 'The resource is created successfully'], 200);
                                }

                            } else {

                                $find = Tercero::find(26);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos - 1;
                                $find->total_price_orders = $find->total_price_orders - $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                return response()->json(['status' => 'The resource is created successfully'], 200);
                            }

                        }
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    return response()->json(['status' => 'order not processed'], 200);
                } else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {
                        foreach ($order['line_items'] as $item) {
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
                        'customer_id' => $order['id'],
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

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            }

            if ($order['cancelled_at'] == null && $order['financial_status'] == 'paid') {

                $send = array();

                if (count($result) > 0) {

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        $customer = Customer::where('email', $order['email'])->first();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                if ($item['product_id'] == 10332997761 && count($customer) > 0) {

                                    $send = [
                                        'form_params' => [
                                            'gift_card' => [
                                                "note" => "Por la compra del producto:  " . $item['title'] . " has recibido este bono. Sigue comprando y ganando con diahello.",
                                                "initial_value" => 1 * $item['quantity'],
                                                "template_suffix" => "gift_cards.birthday.liquid",
                                                "currency" => "COP",
                                                "customer_id" => $customer->customer_id,
                                            ]
                                        ]
                                    ];

                                    //$res = $client->request('post', $api_url . '/admin/gift_cards.json', $send);
                                    //$result = json_decode($res->getBody(), true);

                                    //return response()->json(['status' => 'giff_card created', 'gift_card' => $result], 200);
                                }

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

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

                        if (count($tercero) > 0) {

                            if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if (count($padre) > 0) {

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

                                        return response()->json(['status' => 'The resource is created successfully', $send], 200);
                                    }

                                } else {

                                    return response()->json(['status' => 'The father was not found'], 200);
                                }

                            }

                        } else {

                            $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                            if (count($padre) > 0) {

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

                                    return response()->json(['status' => 'The resource is created successfully'], 200);
                                }

                            } else {

                                $find = Tercero::find(26);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                return response()->json(['status' => 'The resource is created successfully', $send], 200);
                            }

                        }

                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

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

                        if (count($tercero) > 0) {

                            if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if (count($padre) > 0) {

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

                                        return response()->json(['status' => 'The resource is created successfully'], 200);
                                    }

                                } else {

                                    return response()->json(['status' => 'The father was not found'], 200);
                                }

                            }

                        } else {

                            $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                            if (count($padre) > 0) {

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

                                    return response()->json(['status' => 'The resource is created successfully'], 200);
                                }

                            } else {

                                $find = Tercero::find(26);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                return response()->json(['status' => 'The resource is created successfully'], 200);
                            }

                        }

                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

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

                        if (count($tercero) > 0) {

                            if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if (count($padre) > 0) {

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

                                        return response()->json(['status' => 'The resource is created successfully'], 200);
                                    }

                                } else {

                                    return response()->json(['status' => 'The father was not found'], 200);
                                }

                            }

                        } else {

                            $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                            if (count($padre) > 0) {

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

                                    return response()->json(['status' => 'The resource is created successfully'], 200);
                                }

                            } else {

                                $find = Tercero::find(26);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                return response()->json(['status' => 'The resource is created successfully'], 200);
                            }

                        }

                    }

                    return response()->json(['status' => 'order not processed'], 200);

                }

                else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {
                        foreach ($order['line_items'] as $item) {
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
                        'customer_id' => $order['id'],
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

                    $customer = Customer::where('email', $order['email'])->first();

                    if ($order['financial_status'] == "paid") {

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                if ($item['product_id'] == 10332997761 && count($customer) > 0) {

                                    $send = [
                                        'form_params' => [
                                            'gift_card' => [
                                                "note" => "Por la compra del producto:  " . $item['title'] . " has recibido este bono. Sigue comprando y ganando con diahello.",
                                                "initial_value" => 1 * $item['quantity'],
                                                "template_suffix" => "gift_cards.birthday.liquid",
                                                "currency" => "COP",
                                                "customer_id" => $customer->customer_id,
                                            ]
                                        ]
                                    ];

                                    //$res = $client->request('post', $api_url . '/admin/gift_cards.json', $send);
                                    //$result = json_decode($res->getBody(), true);

                                    //return response()->json(['status' => 'giff_card created', 'gift_card' => $result], 200);
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

                        if (count($tercero) > 0) {

                            if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if (count($padre) > 0) {

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

                        } else {

                            $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                            if (count($padre) > 0) {

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

                            } else {

                                $find = Tercero::find(26);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                            }

                        }
                    }

                    $orders_paid = $orders = Logorder::where('status_shopify', 'pending')->where('status_mercadopago', 'approved')->get();
                    foreach ($orders_paid as $paid) {
                        $order_result = Order::where('order_id', $paid->order_id)->where('checkout_id', $paid->checkout_id)->first();
                        if (count($order_result) > 0 ) {
                            if ($order_result->financial_status == 'paid') {
                                $delete = Logorder::where('order_id', $paid->order_id)->where('checkout_id', $paid->checkout_id)->first();
                                if (count($delete) > 0) {
                                    Logorder::find($delete->id)->delete();
                                    DB::table('logsorders')
                                        ->where('id', $delete->id)
                                        ->where('name', '=', $delete->name)
                                        ->where('checkout_id', '=', $delete->checkout_id)
                                        ->where('order_id', '=', $delete->order_id)->delete();
                                }
                            }
                        }
                    }
                    return response()->json(['status' => 'The resource is created successfully', $send], 200);
                }
            }

            if ($order['cancelled_at'] == null && $order['financial_status'] != 'paid') {

                if (count($result) > 0) {

                    if ($result->financial_status == "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
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

                        if (count($tercero) > 0) {

                            if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                if (count($padre) > 0) {

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

                                        return response()->json(['status' => 'The resource is created successfully'], 200);
                                    }

                                } else {

                                    return response()->json(['status' => 'The father was not found'], 200);
                                }

                            }

                        } else {

                            $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                            if (count($padre) > 0) {

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

                                    return response()->json(['status' => 'The resource is created successfully'], 200);
                                }

                            } else {

                                $find = Tercero::find(26);
                                $find->numero_ordenes_referidos = $find->numero_ordenes_referidos - 1;
                                $find->total_price_orders = $find->total_price_orders - $order['total_price'];
                                $find->ganacias = $find->total_price_orders * 0.05;
                                $find->save();

                                return response()->json(['status' => 'The resource is created successfully'], 200);
                            }

                        }
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at == null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }


                        return response()->json(['status' => 'order processed'], 200);
                    }

                    if ($result->financial_status == "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    }

                    if ($result->financial_status != "paid" && $result->cancelled_at != null) {

                        $update = Order::find($result->id);
                        $update->closed_at = $order['closed_at'];
                        $update->cancelled_at = $order['cancelled_at'];
                        $update->cancel_reason = $order['cancel_reason'];
                        $update->financial_status = $order['financial_status'];
                        $update->updated_at = Carbon::parse($order['updated_at']);
                        $update->save();

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) > 0) {
                                    $line_item->date_order = $order['updated_at'];
                                    $line_item->save();
                                }

                            }
                        }

                        return response()->json(['status' => 'order processed'], 200);
                    }

                    return response()->json(['status' => 'order not processed'], 200);

                } else {

                    $tipo_orden = '';
                    $i = 0;
                    $n = 0;

                    if (isset($order['line_items']) && count($order['line_items']) > 0) {
                        foreach ($order['line_items'] as $item) {
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
                        'customer_id' => $order['id'],
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

                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            }
        }
    }
    public function contador()
    {

        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

        $send = [
            'form_params' => [
                'gift_card' => [
                    "note" => "Bono de regalo",
                    "initial_value" => 1,
                    "template_suffix" => "gift_cards.birthday.liquid",
                    "currency" => "COP",
                    "customer_id" => 5894131521 //$customer->customer_id,
                ]
            ]
        ];

        $res = $client->request('post', $api_url . '/admin/gift_cards.json', $send);
        $result = json_decode($res->getBody(), true);

        return $result;



        /*$api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $resa = $client->request('GET', $api_url . '/admin/customers/count.json');
        $countCustomers = json_decode($resa->getBody(), true);


        $result = true;
        $h = 1;

        do {

            $res = $client->request('GET', $api_url . '/admin/customers.json?limit=250&&page=' . $h);

            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 20) {

                usleep(10000000);
            }

            $results = json_decode($res->getBody(), true);

            foreach ($results['customers'] as $customer) {

                $response = Customer::where('network_id', 1)
                    ->where('customer_id', $customer['id'])
                    ->get();

                if(count($response) == 0) {

                    Customer::create([
                        'accepts_marketing' => $customer['accepts_marketing'],
                        'addresses' => $customer['addresses'],
                        'created_at' => Carbon::parse($customer['created_at']),
                        'default_address' => (isset($customer['default_address'])) ? $customer['default_address'] : null,
                        'email' => strtolower($customer['email']),
                        'phone' => $customer['phone'],
                        'first_name' => $customer['first_name'],
                        'customer_id' => $customer['id'],
                        'metafield' => null,
                        'multipass_identifier' => $customer['multipass_identifier'],
                        'last_name' => strtolower($customer['last_name']),
                        'last_order_id' => $customer['last_order_id'],
                        'last_order_name' => $customer['last_order_name'],
                        'network_id' => 1,
                        'note' => $customer['note'],
                        'orders_count' => $customer['orders_count'],
                        'state' => $customer['state'],
                        'tags' => $customer['tags'],
                        'tax_exempt' => $customer['tax_exempt'],
                        'total_spent' => $customer['total_spent'],
                        'updated_at' => Carbon::parse($customer['updated_at']),
                        'verified_email' => $customer['verified_email'],
                    ]);
                }
            }

            $h++;

            if (count($results['customers']) < 1) {
                $result = false;
            }


        } while($result);

        $customersresults = Customer::all();

        foreach ($customersresults as $customer) {

            if ($customer['email'] != 'soportesoyhello@gmail.com') {

                $result = Tercero::where('email', $customer['email'])->get();

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

        return 'terminado';*/









       /* $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

        $orders = Order::where('cancelled_at', null)->where('financial_status', 'paid')->get();

        foreach ($orders as $order) {

            return $order['billing_address']['last_name'];
            $customer = Customer::where('email', $order->email)->first();

            foreach ($order->line_items as $item) {

                if ($item['product_id'] == 10332997761) {

                    $send = [
                        'form_params' => [
                            'gift_card' => [
                                "note" => "Por la compra del producto:  " . $item['title'] . " has recibido este bono. Sigue comprando y ganando con diahello.",
                                "initial_value" => 1 * $item['quantity'],
                                "template_suffix" => "gift_cards.birthday.liquid",
                                "currency" => "COP",
                                "customer_id" => 5894131521 //$customer->customer_id,
                            ]
                        ]
                    ];

                    $res = $client->request('post', $api_url . '/admin/gift_cards.json', $send);
                    $result = json_decode($res->getBody(), true);

                    return $result;

                }

            }
        }*/

        /*$api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

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

            foreach ($results['customers'] as $customer) {
                $response = Customer::where('network_id', 1)
                    ->where('customer_id', $customer['id'])
                    ->get();

                if(count($response) == 0) {

                    Customer::create([
                        'accepts_marketing' => $customer['accepts_marketing'],
                        'addresses' => $customer['addresses'],
                        'created_at' => Carbon::parse($customer['created_at']),
                        'default_address' => (isset($customer['default_address'])) ? $customer['default_address'] : null,
                        'email' => strtolower($customer['email']),
                        'phone' => $customer['phone'],
                        'first_name' => $customer['first_name'],
                        'customer_id' => $customer['id'],
                        'metafield' => null,
                        'multipass_identifier' => $customer['multipass_identifier'],
                        'last_name' => strtolower($customer['last_name']),
                        'last_order_id' => $customer['last_order_id'],
                        'last_order_name' => $customer['last_order_name'],
                        'network_id' => 1,
                        'note' => $customer['note'],
                        'orders_count' => $customer['orders_count'],
                        'state' => $customer['state'],
                        'tags' => $customer['tags'],
                        'tax_exempt' => $customer['tax_exempt'],
                        'total_spent' => $customer['total_spent'],
                        'updated_at' => Carbon::parse($customer['updated_at']),
                        'verified_email' => $customer['verified_email'],
                    ]);
                }
            }

            $customersresults = Customer::all();

            foreach ($customersresults as $customer) {

                if ($customer['email'] != 'soportesoyhello@gmail.com') {

                    $result = Tercero::where('email', $customer['email'])->get();

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
     
        }*/





        /*$api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

        define('CLIENT_ID', "7134341661319721");
        define('CLIENT_SECRET', "b7cQUIoU5JF4iWVvjM0w1YeX4b7VwLpw");
        $mp = new MP(CLIENT_ID, CLIENT_SECRET);
        define('payments', '/v1/payments/search?external_reference=');
        define('access', '&access_token=');
        define('ACCESS_TOKEN', $mp->get_access_token());
        $orders = Order::where('financial_status', 'pending')->where('cancelled_at', null)->get();
        $contador = 0;


        $custom = $client->request('GET', $api_url . '/admin/custom_collections/count.json');
        $custom_count = json_decode($custom->getBody(), true);

        $pagesNumberCustom = (int)$custom_count['count']/250;
        $numberCustom = explode( '.', $pagesNumberCustom);
        $enteraCustom = (int)$numberCustom[0];
        $decimalCustom = (int)$numberCustom[1];


        if($decimalCustom !== 0) {
            $enteraCustom = $enteraCustom + 1;
        }

        for ($i = 1; $i <= $enteraCustom; $i++) {

            $resb = $client->request('GET', $api_url . '/admin/custom_collections.json?limit=250&&page=' . $i);
            $results = json_decode($resb->getBody(), true);

            $headersb = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headersb[0]);
            $diferenciab = $x[1] - $x[0];

            if ($diferenciab < 10) {
                usleep(10000000);
            }


            foreach ($results['custom_collections'] as $collection) {
                //$collection['title'];

                $result = true;
                $h = 1;

                do {
                    $resb = $client->request('GET', $api_url . '/admin/collects.json?collection_id='.$collection['id'] .'&&fields=product_id&&limit=250&&page=' . $h);

                    $products_count = json_decode($resb->getBody(), true);

                    $headersa = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                    $x = explode('/', $headersa[0]);
                    $diferenciaa = $x[1] - $x[0];

                    if ($diferenciaa < 10) {
                        usleep(10000000);
                    }

                    foreach ($products_count['collects'] as $collect) {
                        echo "Product_id: " . $collect['product_id'] . " - Collection: " . strtolower($collection['title']) . "\n";
                    }

                    $h++;

                    if (count($products_count['collects']) < 1) {
                        $result = false;
                    }


                } while($result);

                return "echo";
            }
        }


        $smart = $client->request('GET', $api_url . '/admin/smart_collections/count.json');
        $smart_count = json_decode($smart->getBody(), true);

        $pagesNumberSmart = (int)$smart_count['count']/250;
        $numberSmart = explode( '.', $pagesNumberSmart);
        $enteraSmart = (int)$numberSmart[0];
        $decimalSmart = (int)$numberSmart[1];


        if($decimalSmart !== 0) {
            $enteraSmart = $enteraSmart + 1;
        }

        for ($i = 1; $i <= $enteraSmart; $i++) {

            $resa = $client->request('GET', $api_url . '/admin/smart_collections.json?limit=250&&page=' . $i);
            $results = json_decode($resa->getBody(), true);

            $headers = $resa->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];

            if ($diferencia < 10) {
                usleep(10000000);
            }

            foreach ($results['smart_collections'] as $collection) {
                //$collection['title'];

                $result = true;
                $h = 1;

                do {
                    $resb = $client->request('GET', $api_url . '/admin/collects.json?collection_id='.$collection['id'] .'&&fields=product_id&&limit=250&&page=' . $h);

                    $products_count = json_decode($resb->getBody(), true);

                    $headersa = $resb->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                    $x = explode('/', $headersa[0]);
                    $diferenciaa = $x[1] - $x[0];

                    if ($diferenciaa < 10) {
                        usleep(10000000);
                    }

                    foreach ($products_count['collects'] as $collect) {
                        echo "Product_id: " . $collect['product_id'] . " - Collection: " . strtolower($collection['title']) . "\n";
                    }

                    $h++;

                    if (count($products_count['collects']) < 1) {
                        $result = false;
                    }


                } while($result);

                return "echo";
            }
        }*/



        //return $smart_count['count'] . '-' . $custom_count['count'];

        /*foreach ($orders as $order) {

            $result = array();

            $res = $client->request('GET', $api_url . '/admin/orders/'.$order->order_id.'.json');
            $order_shopify = json_decode($res->getBody(), true);
            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 10) {
                usleep(10000000);
            }

            if ($order_shopify['order']['financial_status'] == 'pending' && $order_shopify['order']['cancelled_at'] == null && $order_shopify['order']['cancel_reason'] == null) {

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

                    $payment_method_id = $result['response']['results'][0]['payment_method_id'];
                    $status = $result['response']['results'][0]['status'];
                    $date_created = Carbon::parse($result['response']['results'][0]['date_created']);
                    $today = Carbon::now();
                    $diferencia = $today->day - $date_created->day;

                    if ($payment_method_id  == 'efecty' && $status == 'pending') {

                        if ($today->year == $date_created->year) {

                            if ($today->month < $date_created->month) {

                                return $result['response']['results'][0];
                            }

                            if ($today->month == $date_created->month) {

                                if ($diferencia > 2) {

                                    return $result['response']['results'][0];
                                }
                            }
                        }

                        if ($today->year < $date_created->year) {

                            if ($today->month > 1 ) {

                                return $result['response']['results'][0];
                            }

                            if ($today->month == 1 && $date_created->month < 12) {

                                return $result['response']['results'][0];
                            }

                            if ($today->month == 1 && $date_created->month == 12) {

                                if ($today->day > 2) {

                                    return $result['response']['results'][0];
                                }

                                if ($today->day == 2 && $date_created->day < 31) {

                                    return $result['response']['results'][0];
                                }

                                if ($today->day < 2 && $date_created->day < 30) {

                                    return $result['response']['results'][0];
                                }
                            }
                        }
                    }

                    if ($payment_method_id  == 'efecty' && $status == 'cancelled') {

                        return $result['response']['results'][0];
                    }

                    if ($payment_method_id  != 'efecty' && ($status == 'cancelled' || $status == 'rejected')) {

                        return $result['response']['results'][0];

                    }
                }
            }

            if ($order_shopify['order']['financial_status'] == 'paid') {

                $update = Order::find($order->id);
                $update->financial_status = $order_shopify['order']['financial_status'];
                $update->updated_at = Carbon::parse($order_shopify['order']['updated_at']);
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
                                                        'value' => '' . ( $find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
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

        }*/
    }
}