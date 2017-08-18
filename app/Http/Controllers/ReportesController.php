<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\Tercero;
use App\Entities\Network;
use App\Order;
use App\Customer;
use App\Product;
use App\Entities\OrdenesResumen;
use App\Entities\OrdenesResumenDetalle;
use App\Entities\Envio;
use DB;
use Excel;
use Yajra\Datatables\Datatables;

class ReportesController extends Controller {
    
	public function index() 
        {
            return view('admin.reportes.index');
	}
        
        public function code () 
        {
            return view('admin.reportes.code');
        }
        
        public function anyCode() 
        {
             $add = array();
             $customers = Customer::all();
        
                foreach ($customers as $customer) {
                    $finder = Customer::find($customer->id);
                    if (isset($finder['addresses']) && count($finder['addresses']) > 0) {
                        if (strtolower($customer->last_name) != strtolower($finder['addresses'][0]['last_name'])) {
                            array_push($add, $customer);
                        }
                    }

                }
                
            $send = collect($add);
            return Datatables::of($send)
                ->addColumn('id', function ($send) {
                    return '<div align=left>' . $send['id'] . '</div>';
                })
                ->addColumn('name', function ($send) {
                    return '<div align=left>' . $send['first_name'] . '</div>';
                })
                ->addColumn('code', function ($send) {
                    return '<div align=left>' . $send['last_name'] . '</div>';
                })
                ->addColumn('email', function ($send) {
                    return '<div align=left>' . $send['email'] . '</div>';
                })
                ->addColumn('addresses', function ($send) {
                    return '<div align=left>' . $send['addresses'][0]['last_name'] . '</div>';
                })
                ->make(true);
        }

        public function products()
        {
            $products = Product::select('id', 'image')->get();
            
            
            $totals = array();
            foreach ($products as $product) {
                if (count($product['image']['src']) === 0) {
                    $finder = Product::find($product['id']);
                    array_push($totals, $finder);
                } 
            }
           
            $send = collect($totals);
            return Datatables::of($send)
                ->addColumn('id', function ($send) {
                    return '<div align=left>' . $send['id'] . '</div>';
                })
                ->addColumn('title', function ($send) {
                    return '<div align=left>' . $send['title'] . ' </div>';
                })
                
                ->make(true);
        }
        
        public function anyData()
        {
             
           $referidos  = DB::table('terceros')
                            ->where('numero_referidos', '>', 0)
                            ->where('numero_ordenes_referidos', '>', 0)
                            ->where('total_price_orders', '>', 0)
                            ->select('id', 'nombres', 'email', 'total_price_orders')
                            ->get();

            $report = array();
            
            foreach ($referidos as $referido) {
                
               
                $aux = [
                    'id' => $referido->id,
                    'name' => $referido->nombres,
                    'email' => $referido->email,
                    'total' => $referido->total_price_orders,
                    'ganancia' => $referido->total_price_orders * 0.05
                ];
               
                array_push($report, $aux);
            }
            
            $send = collect($report);
            return Datatables::of($send)
                ->addColumn('id', function ($send) {
                    return '<div align=left> '. $send['id'] .'</div>';
                })
                ->addColumn('nombres', function ($send) {
                    return '<div align=left>' . $send['name'] . '</div>';
                })
                ->addColumn('email', function ($send) {
                    return '<div align=left>' . $send['email'] . '</div>';
                })
                ->addColumn('total', function ($send) {
                    return '<div align=left>' . number_format($send['total']) . '</div>';
                })
                
                ->addColumn('ganancia', function ($send) {
                    return '<div align=left>' . number_format($send['ganancia']) . '</div>';
                })
                ->make(true);
        }
        
        
	public function datos(Request $req) {
            $entregas = Estado::select('id','nombre','alias')->where('padre_id','2')->get();
            $devoluciones = Estado::select('id','nombre','alias')->where('padre_id','3')->get();    

            //dd($req->reporte);

                        if ($req->reporte==1) {
                $resumenes = OrdenesResumen::select('orden_id','cantidad','entregas','devoluciones','retenciones')->with(array('detalle' => function($query) use ($req)
                    {
                       $query->select(DB::raw('estado_id,padre_id,orden_id,sum(cantidad) as cantidad'))->groupBy('estado_id','padre_id','orden_id')->orderBy('estado_id');
                    }))->with(array('orden' => function($query) use ($req)
                    {
                       $query->select('id','numero','cliente_id','producto_id')->where('fecha','>=',$req->desde)->with('cliente')->with('producto')->where('fecha','<=',$req->hasta);
                    }))->paginate(10);
              //dd($resumenes);
            return view('admin.reportes.resultados',compact('resumenes','entregas','devoluciones','req'));

            } else if ($req->reporte==2) {
                $resumenes = OrdenesResumen::select(DB::raw('cliente_id,sum(cantidad) as cantidad,sum(entregas) as entregas,sum(devoluciones) as devoluciones,sum(retenciones) as retenciones'))->with(array('detalle' => function($query) use ($req)
                    {
                       $query->select(DB::raw('estado_id,padre_id,orden_id,sum(cantidad) as cantidad'))->groupBy('estado_id','padre_id','orden_id')->orderBy('estado_id');
                    }))->join('ordenes','ordenes_resumen.orden_id','=','ordenes.id')->where('fecha','>=',$req->desde)->where('fecha','<=',$req->hasta)->groupBy('cliente_id')->paginate(10);
            } else if ($req->reporte==3) {
                $resumenes = OrdenesResumenDetalle::select(DB::raw('destino_id,sum(cantidad) as cantidad,sum(case when ordenes_resumen_detalle.padre_id=2 then cantidad else 0 end) as entregas,sum(case when ordenes_resumen_detalle.padre_id=3 then cantidad else 0 end) as devoluciones,sum(case when ordenes_resumen_detalle.padre_id=4 then cantidad else 0 end) as retenciones'))
                  ->join('ordenes','ordenes_resumen_detalle.orden_id','=','ordenes.id')->where('fecha','>=',$req->desde)
                  ->with(array('detalle' => function($query) use ($req)
                    {
                       $query->select(DB::raw('destino_id,estado_id,padre_id,sum(cantidad) as cantidad'))->groupBy('estado_id','padre_id','destino_id')->orderBy('estado_id');
                    }))->with('destino')->where('fecha','<=',$req->hasta)->groupBy('destino_id')->paginate(10);
            } else if ($req->reporte==4) {
                $resumenes = OrdenesResumenDetalle::select(DB::raw('courier_id,sum(cantidad) as cantidad,sum(case when ordenes_resumen_detalle.padre_id=2 then cantidad else 0 end) as entregas,sum(case when ordenes_resumen_detalle.padre_id=3 then cantidad else 0 end) as devoluciones,sum(case when ordenes_resumen_detalle.padre_id=4 then cantidad else 0 end) as retenciones'))
                  ->join('ordenes','ordenes_resumen_detalle.orden_id','=','ordenes.id')->where('fecha','>=',$req->desde)
                  ->with(array('detalle_courier' => function($query) use ($req)
                    {
                       $query->select(DB::raw('courier_id,estado_id,padre_id,sum(cantidad) as cantidad'))->groupBy('estado_id','padre_id','courier_id')->orderBy('estado_id');
                    }))->where('fecha','<=',$req->hasta)->groupBy('courier_id')->paginate(10);

            }


            return view('admin.reportes.resultados',compact('resumenes','entregas','devoluciones','req'));
          }

  public function descargar(Request $req) {

    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 300);
    // $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
    // $cacheSettings = array( 'memoryCacheSize' => '256M');
    // PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

    $envios = Envio::select('idenvio', 'cuenta','destinatario','direccion','telefono')->join('estados','envios.estado_id','=','estados.id')->where('estados.padre_id',$req->padre_id)
      //if ($req->estado_id) 
      //->where('estado_id',$req->estado_id)
      //->with(
      // array('estado' => function($query) use ($req)
      //       {
      //          $query->select(DB::raw('nombre,padre_id'))->where('padre_id',$req->padre_id);
      //       }))
      ->where('orden_id',$req->orden_id)->get();

    //dd($envios);
  
    Excel::create('envios', function($excel) use($envios) {
        $excel->sheet('Sheet 1', function($sheet) use($envios) {
            $sheet->fromArray($envios);
        });
    })->export('csv');
  }
}