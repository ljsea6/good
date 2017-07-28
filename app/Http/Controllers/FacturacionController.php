<?php
namespace App\Http\Controllers;

use App\Entities\Orden;
use App\Entities\OrdenesServicios;
use App\Entities\OrdenesResumen;
use App\Entities\OrdenesResumenDetalle;
use App\Entities\Factura;
use App\Entities\FacturaDetalle;
use App\Entities\Resolucion;
use App\Entities\Tercero;
use App\Entities\Tipo;
use App\Entities\Servicio;
use App\Entities\Estado;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;
use DB;
use Mail;


class FacturacionController extends Controller {

	public function __construct() {
		Carbon::setLocale('es');
	}

	public function index() {
		return view('admin.facturacion.index');
	}

	public function anyData()
    {
        $facturas = Factura::with('cliente')->orderby('fecha','desc');//->//select("numero,fecha,cliente.nombres || ' ' || cliente.apellidos as n");

        //dd($facturas);

        return Datatables::of($facturas)
            ->editColumn('fecha', function ($facturas) {
                return $facturas->fecha ? with(new Carbon($facturas->fecha))->format('d-m-Y h:i') : '';
            })
            ->editColumn('tipo', function ($facturas) {
            	if ($facturas->tipo==1) {
                    return 'Contado';
                } else {
                	return 'Credito';
                }
            })       
            ->make(true);
     }
          
       
	public function create() {
		$clientes     = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
		
		return view('admin.facturacion.create', compact('clientes'));
	}

    public function buscar(Request $req) {
 			$resumenes = OrdenesResumenDetalle::select(DB::raw('orden_id,tipos.id,tipos.nombre,sum(case when ordenes_resumen_detalle.padre_id=2 then cantidad else 0 end) as entregas,sum(case when ordenes_resumen_detalle.padre_id=3 then cantidad else 0 end) as devoluciones'))->join('ciudades','ordenes_resumen_detalle.destino_id', '=', 'ciudades.id')->join('tipos','ciudades.tipo_id', '=', 'tipos.id')->with(array('orden' => function($query) use ($req)
            {
               $query->select('id','numero','cliente_id','producto_id')->with('cliente')->with('producto')->whereNull('facturada')->where('fecha','>=',$req->desde)->where('fecha','<=',$req->hasta);
               //dd($req->cliente);
               if ($req->cliente) {
                   $query->where('cliente_id','=',$req->cliente);
               }
            }))->groupBy('tipos.id','tipos.nombre','orden_id')->paginate(10);

            //(dd($resumenes->orden));
    
        	return view('admin.facturacion.resultados',compact('resumenes','req'));   	
	}

	public function store(Request $req) {	
			$resumenes = OrdenesResumenDetalle::select(DB::raw('orden_id,tipos.id,tipos.nombre,sum(case when ordenes_resumen_detalle.padre_id=2 then cantidad else 0 end) as entregas,sum(case when ordenes_resumen_detalle.padre_id=3 then cantidad else 0 end) as devoluciones'))->join('ciudades','ordenes_resumen_detalle.destino_id', '=', 'ciudades.id')->join('tipos','ciudades.tipo_id', '=', 'tipos.id')->with(array('orden' => function($query) use ($req)
            {
               $query->select('id','numero','cliente_id','producto_id')->with('cliente')->with('producto')->whereNull('facturada')->where('fecha','>=',$req->desde)->where('fecha','<=',$req->hasta);
               //dd($req->cliente);
               if ($req->cliente) {
                   $query->where('cliente_id','=',$req->cliente);
               }
            }))->groupBy('tipos.id','tipos.nombre','orden_id')->paginate(10);

			foreach ($resumenes as $resumen) {
				$factura = Factura::where('cliente_id',$resumen->orden->cliente->id);
				if ($factura) {


					$factura = new Factura;
					
					$factura->fecha     = "now()";
					$factura->numero    = "0000".$factura->id;
					$factura->valor     = 0;
					$factura->iva     = 0;
					$factura->descuento     = 0;
					$factura->resolucion_id     = 1;
					$factura->tipo    = 2;
					$factura->cliente_id   = $resumen->orden->cliente->id;
					$factura->usuario_id   = currentUser()->id;
          $factura->ciudad_id   = currentUser()->ciudad_id;
					$factura->save();
				} 
				$id = $factura->id;
				$factura->numero    = $id;
				$factura->save();


				$factura =  new FacturaDetalle;
				$factura->factura_id    = $id;
				$factura->valor     = ftarifas_tercero_facturacion($resumen->orden->cliente->id,1,7,24,13,$resumen->id,15)*($resumen->entregas+$resumen->entregas);
				$factura->cantidad     = ($resumen->entregas+$resumen->entregas);
				//$factura->tipo_mensajeria     = $resumen->id;
				//$factura->tipo_producto     = $resumen->id;
				//$factura->tipo_envio     = $resumen->id;
				$factura->tipo_servicio_id     = $resumen->id;
				$factura->tipo_tiempo_id     = 1;
				$factura->save();

				// $orden = Orden::find($resumen->orden_id);
				// $orden->facturada = "now()";
				// $orden->save();
			}



      return view('admin.facturacion.index',compact('resumenes','req'));   	
	}

	public function imprimir($id,$email) {
		$factura     = Factura::with('cliente')->with('ciudad')->find($id);
 		//dd($factura);
 		$data = $this->getData();
        $date = $factura->fecha ? with(new Carbon($factura->fecha))->format('d-m-Y h:i') : '';;
        $invoice = "2222";
        $cliente="Ana";
        $ciudad="Bogota";

        //dd($factura);

        $invoice = [
           'numero'=>$factura->numero,
           'cliente'=>$factura->cliente->nombres.' '.$factura->cliente->apellidos,
           'identificacion'=>$factura->cliente->identificacion,
           'ciudad'=>$factura->cliente->ciudad->nombre,
           'direccion'=>$factura->cliente->direccion,
           'telefono'=>$factura->cliente->telefono,
           'valor'=>$factura->valor,
           ];

        $detalle     = FacturaDetalle::select(DB::raw('facturas_detalle.id,servicios.nombre as servicio,null,facturas_detalle.cantidad,facturas_detalle.valor'))->join('servicios','facturas_detalle.servicio_id', '=', 'servicios.id')->where('factura_id',$id)->where('cantidad','>',0);

        $detalle     = FacturaDetalle::select(DB::raw('facturas_detalle.id,tipos.nombre as servicio,tiempo.nombre as tiempo,cantidad,valor'))->join('tipos','facturas_detalle.tipo_servicio_id', '=', 'tipos.id')->join('tipos as tiempo','facturas_detalle.tipo_tiempo_id', '=', 'tiempo.id')->where('factura_id',$id)->where('cantidad','>',0)->union($detalle)->orderby('id')->limit('10')->get()->toArray();


        //dd($detalle);
        $data = $detalle;

        $view =  \View::make('pdf.invoice', compact('data', 'date', 'invoice'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);

        if ($email==1) {		
        		$GLOBALS["men"]="Se adjunta factura";

        		Mail::raw($GLOBALS["men"], function($message)  use ($pdf)
                            {
                                $message->from('oscarfonseca@vincom.co','Informes Domina');
                                //$message->to($tercero->email,$tercero->nombres.' '.$tercero->apellidos)->subject('Orden procesada');
                                $message->to('oscarfonseca@vincom.co','Oscar Fonseca')->subject('Factura Generada');
                                $message->attachData($pdf->output(), "factura.pdf");
                    });
        } else {
            return $pdf->stream('invoice');
        }
	}

	public function getData() 
    {
        $data =  [
            'quantity'      => '1' ,
            'description'   => 'some ramdom text',
            'price'   => '500',
            'total'     => '500'
        ];
        return $data;
    }

}
