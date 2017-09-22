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
        $orders = Order::where('financial_status', 'paid')->get();
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
        $orders = Order::where('financial_status', 'pending')->get();
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
            $orders = Order::where('financial_status', 'paid')->where('tipo_orden', 'nacional')->get();
            $send = collect($orders);
            return Datatables::of($send )
                ->addColumn('name', function ($send) {
                    return '<div align=left>' . $send->name . '</div>';
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
                ->addColumn('order', function ($send) {
                    $result = '';
                    foreach ($send->line_items as $item ){
                        $product = Product::find($item['product_id']);
                        if(count($product['image']) > 0 && count($product['images']) > 0) {
                            $result .= '<div class="container" style="width: 100%">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Nombre: ' . $item['title'] . '</strong></p>
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
                                <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">#'. $send->order_number .'</button>
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
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">#'. $send->order_number .'</button>
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
                    $product = Product::find($send->line_items[0]['product_id']);
                    if (!isset($product->tipo_producto) || $product->tipo_producto == null || $product->tipo_producto == '') {
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
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
                                            <h4 class="modal-title" style="color: #f60620">Orden #'. $send->order_number .'</h4>
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
                    if (isset($product->tipo_producto) && $product->tipo_producto == 'nacional') {
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$product->tipo_producto.' #'. $send->order_number .'</h4>
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
                    if (isset($product->tipo_producto) && $product->tipo_producto == 'internacional') {
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                        <a  type="button" class="btn btn-primary btn-circle">5</a>
                                                        <p>Entregado</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                            <h4 class="modal-title" style="color: #f60620">Orden '.$product->tipo_producto.' #'. $send->order_number .'</h4>
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
                    return '<div align=left><a href="/admin/orders/'. $send->id .'/edit"  class="btn btn-danger btn-xs text-center">Comprar</a></div>';
                })
                ->make(true);
        }
        else {
            $orders = Order::where('financial_status', 'paid')->get();
            $send = collect($orders);
            return Datatables::of($send )
                ->addColumn('name', function ($send) {
                    return '<div align=left>' . $send->name . '</div>';
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
                ->addColumn('order', function ($send) {
                    $result = '';
                    foreach ($send->line_items as $item ){
                        $product = Product::find($item['product_id']);
                        if(count($product['image']) > 0 && count($product['images']) > 0) {
                            $result .= '<div class="container" style="width: 100%">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Nombre: ' . $item['title'] . '</strong></p>
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
                                <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">#'. $send->order_number .'</button>
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
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->order_number .'">#'. $send->order_number .'</button>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                                                         <p><strong>Fecha Pendiente: ' . Carbon::parse($send->created_at)->toFormattedDateString() . '</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong>Fecha Compra: ' . Carbon::parse($send->fecha_compra)->toFormattedDateString() . '</strong></p>
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
                        return '<div align=left><a href="/admin/orders/'. $send->id .'/edit"  class="btn btn-danger btn-xs text-center">Comprar</a></div>';
                    } else {
                        return '<div align=left><a href="/admin/orders/'. $send->id .'/edit"  class="btn btn-danger btn-xs text-center">Envio</a></div>';
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
            $result = Order::where('order_id', $order['id'])
                ->where('email', $order['email'])
                ->where('network_id', 1)
                ->first();
            if (count($result) == 0) {
                $owner = Customer::where('email', $order['email'])->first();
                if (count($owner) > 0) {
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
                                $product = Product::find($item['product_id']);
                                if (count($product) > 0) {
                                    $product->precio_unidad = $item['price'];
                                    $product->unidades_vendidas = $product->unidades_vendidas + $item['quantity'];
                                    $product->save();
                                }
                            }
                        }
                        $tercero = Tercero::with('networks')->where('email', $order['email'])->first();
                        if (count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {
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
                    return response()->json(['status' => 'The resource is created successfully'], 200);
                }
            } else {
                return response()->json(['status' => 'order not processed'], 200);
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
            if ($order['financial_status'] == 'paid') {
                $result = Order::where('order_id', $order['id'])
                    ->where('email', $order['email'])
                    ->where('network_id', 1)
                    ->first();
                if (count($result) > 0) {
                    if ($result->financial_status != "paid") {
                        $update = Order::find($result->id);
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
                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    } else {
                        return response()->json(['status' => 'order not processed'], 200);
                    }
                } else {
                    $owner = Customer::where('email', $order['email'])->first();
                    if (count($owner) > 0) {
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
                                    $product = Product::find($item['product_id']);
                                    if (count($product) > 0) {
                                        $product->precio_unidad = $item['price'];
                                        $product->unidades_vendidas = $product->unidades_vendidas + $item['quantity'];
                                        $product->save();
                                    }
                                }
                            }
                            $tercero = Tercero::with('networks')->where('email', $order['email'])->first();
                            if (count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {
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
                        return response()->json(['status' => 'The resource is created successfully'], 200);
                    }
                }
            } else {
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
                return response()->json(['status' => 'order not processed'], 200);
            }
        }
    }
    public function delete()
    {
        /*
        $input = file_get_contents('php://input');
        $order = json_decode($input, true);
        if ($order['financial_status'] !== 'paid') {
            Order::where('order_id', (int) $order['id'])
                    ->where('email', $order['email'])
                    ->where('network_id', 1)
                    ->delete();
            $product = Product::where('id', $order['line_items'][0]['product_id'])->get();
            if (count($product) > 0) {
                $find = Product::find($product[0]['id']);
                $find->unidades_vendidas = $find->unidades_vendidas - 1;
                $find->save();
            }
            $resultaux = Customer::where('email', strtolower($order['email']))
                    ->where('customer_id', $order['customer']['id'])
                    ->where('network_id', 1)
                    ->get();
            if (count($resultaux) > 0) {
                $tercero = Tercero::where('email', $resultaux[0]['last_name'])->where('state', true)->first();
                if (count($tercero) > 0) {
                    $find = Tercero::find($tercero->id);
                    DB::table('terceros')->where('id', $find->id)->update(['total_price_orders' => $find->total_price_orders - $order['total_price']]);
                    DB::table('terceros')->where('id', $find->id)->update(['numero_ordenes_referidos' => $find->numero_ordenes_referidos - 1]);
                }
                if (count($tercero) == 0) {
                    $find = Tercero::find(26);
                    DB::table('terceros')->where('id', 26)->update(['total_price_orders' => $find->total_price_orders - $order['total_price']]);
                    DB::table('terceros')->where('id', 26)->update(['numero_ordenes_referidos' => $find->numero_ordenes_referidos - 1]);
                }
            }
            return response()->json(['status' => 'The resource is created successfully'], 200);
        }
        *
         */
    }
    public function contador()
    {

        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $order = Order::find(61309);
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
                        return json_decode($res->getBody(), true);
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
                            return json_decode($res->getBody(), true);
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
                        return json_decode($res->getBody(), true);
                    }
                }
            }

        }





       /* $res = $client->request('get', $api_url . '/admin/orders/5599849793/fulfillments.json');
        $order = json_decode($res->getBody(), true);
        return $order;  */

      /* $res = $client->request('post', $api_url . '/admin/orders/5599849793/fulfillments/4862722433/events.json', array(
                                        'form_params' => array(
                                            'event' => array(
                                                "status" => "delivered"
                                            )
                                        )
                                    )
                                );*/




        /*$user = Tercero::find(174802);
        $contabilidadRole = Role::find(19);
        //$user->attachRole($contabilidadRole);
        if ($user->is('contabilidad|servicio.al.cliente'))
            return response()->json('Es contador');
        else
            return response()->json('No es contador');*/
        /* $orders = Order::where('financial_status', 'paid')->get();
        $products = Product::all();
        $contador = 0;
        $cont = 0;
        foreach ($products as $product) {
            $contador++;
            foreach ($orders as $order) {
                if (isset($order->line_items) && count($order->line_items) > 0) {
                    foreach ($order->line_items as $item) {
                        if ($item['product_id'] == $product->id) {
                            $cont = $cont + $item['quantity'];
                        }
                    }
                }
            }
            if ($contador == 200) {
                usleep(1000000);
                $contador = 0;
            }
            $update = Product::find($product->id);
            $update->unidades_vendidas = $cont;
            $update->save();
            $cont = 0;
        }
        return 'echo';*/
        /*$totalProducts = array();
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api_url . '/admin/collects/count.json?collection_id=338404417');
        $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
        $x = explode('/', $headers[0]);
        $diferencia = $x[1] - $x[0];
        if ($diferencia < 10) {
            usleep(10000000);
        }
        $countProcucts = json_decode($res->getBody(), true);
        $pagesNumber = $countProcucts['count']/250;
        $number = explode( '.', $pagesNumber);
        $entera = (int)$number[0];
        $decimal = (int)$number[1];
        if($decimal !== 0) {
            $entera = $entera + 1;
        }
        for ($i = 1; $i <= $entera; $i++) {
            $res = $client->request('GET', $api_url . '/admin/collects.json?collection_id=338404417&fields=product_id&limit=250&&page=' . $i);
            $results = json_decode($res->getBody(), true);
            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 10) {
                usleep(10000000);
            }
            foreach ($results['collects'] as $p) {
                array_push($totalProducts, $p);
            }
        }
        $results = array();
        foreach ($totalProducts as $item) {
            array_push($results, $item['product_id']);
        }*/
        /*$orders = Order::all();
        $i = 0;
        $n = 0;
        foreach ($orders as $order) {
            $i = 0;
            $n = 0;
            if (isset($order->line_items) && count($order->line_items) > 0) {
                foreach ($order->line_items as $item) {
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
                $update = Order::find($order->id);
                $update->tipo_orden = 'nacional/internacional';
                $update->save();
                $i = 0;
                $n = 0;
            }
            if ($i > 0 && $n == 0) {
                $update = Order::find($order->id);
                $update->tipo_orden = 'internacional';
                $update->save();
                $i = 0;
                $n = 0;
            }
            if ($i == 0 && $n > 0) {
                $update = Order::find($order->id);
                $update->tipo_orden = 'nacional';
                $update->save();
                $i = 0;
                $n = 0;
            }
        }
        return 'echo';*/
        /* $user = Tercero::find(22);
         $role = Role::find(0);
         $user->attachPermission(21);*/
        /*define('CLIENT_ID', "7134341661319721");
        define('CLIENT_SECRET', "b7cQUIoU5JF4iWVvjM0w1YeX4b7VwLpw");
        $mp = new MP(CLIENT_ID, CLIENT_SECRET);
        define('payments', '/v1/payments/search?external_reference=');
        define('access', '&access_token=');
        define('ACCESS_TOKEN', $mp->get_access_token());
        $orders = Order::where('financial_status', 'pending')->get();
        $contador = 0;
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
                    if ($result['response']['results'][0]['payment_method_id'] == 'efecty') {
                        $days = Carbon::parse($result['response']['results'][0]['date_created'])->diffInDays();
                        if ($days > 2) {
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
                    } else {
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
        }*/
    }
}