<?php

use App\Entities\ClientesEstado;
use App\Entities\ClientesServicio;
use App\Entities\ClientesTarifa;
use App\Entities\ClientesTiempo;
use App\Entities\OrdenesResumen;
use App\Entities\OrdenesServicio;
use App\Entities\Sucursal;
use App\Entities\Tarifa;
use App\Entities\Tercero;
use App\Entities\Servicio;

/**
 * [currentUser usuario logeado]
 * @return App\Entities\Tercero
 */
function currentUser() {
    return auth()->user();
}

function currentSucursal() {
    $sucursal_id = auth()->user()->sucursal_id;
    $sucursal    = Sucursal::select('id', 'nombre')->where('id', $sucursal_id)->first();
    $sucursal    = $sucursal->nombre;
    return $sucursal;
}

function Buscar_estado_cliente($estado, $cliente_id) {
    $est = ClientesEstado::select('estado_cliente')->where('estado_original', $estado)->where('cliente_id', $cliente_id)->first();

    if ($est) {
        return $est->estado_cliente;
    } else {
        return $estado;
    }

}

function Buscar_tiempos_cliente($tipo_tiempo_id, $cliente_id, $tiempo) {
    $est = ClientesTiempo::select('tiempo')->where('tipo_tiempo_id', $tipo_tiempo_id)->where('cliente_id', $cliente_id)->first();

    if ($est) {
        return $est->tiempo;
    } else {
        return $tiempo;
    }

}

function ftarifas_tercero($cliente_id, $tipo_pago, $tipoMensajeria, $tipoProd, $tipoTiempo, $tipoDest, $cantidad) {
    $tarifa = ClientesTarifa::select('valor')->where('cliente_id', $cliente_id)->where('cantidad_desde_hasta', $cantidad)->where('tipo_mensajeria_id', $tipoMensajeria)->where('tipo_producto_id', $tipoProd)->where('tipo_tiempo_id', $tipoTiempo)->where('tipo_ciudad_id', $tipoDest)->where('tipo_pago_id',$tipo_pago)->first();

    if (!$tarifa) {
        $valor = 0;
    } else {
        $valor = $tarifa->valor;
    }

    return round($valor);
}

function ftarifas_tercero_facturacion($cliente_id, $tipo_pago, $tipoMensajeria, $tipoProd, $tipoTiempo, $tipoDest, $cantidad) {
    $tarifa = ClientesTarifa::select('valor')->where('cliente_id', $cliente_id)->whereraw('cantidad_desde_hasta @> ' . $cantidad)->where('tipo_mensajeria_id', $tipoMensajeria)->where('tipo_producto_id', $tipoProd)->where('tipo_tiempo_id', $tipoTiempo)->where('tipo_ciudad_id', $tipoDest)->first();

//dd($tarifa);
    if (!$tarifa) {
        $valor = 0;
    } else {
        $valor = $tarifa->valor;
    }

    return round($valor);
}

function ftarifas_recogidas($cliente_id, $tipo_pago, $tipoMensajeria, $tipoProd, $tipoTiempo, $tipoDest, $cantidad) {
    //Traemos el rango que aplica tomando en cuenta el total, sin tener en cuenta el destino.
    $tarifa = ClientesTarifa::select('tipo_ciudad_id', 'valor')->where('cliente_id', $cliente_id)->whereraw('cantidad_desde_hasta @> ' . $cantidad)->where('tipo_mensajeria_id', $tipoMensajeria)->where('tipo_producto_id', $tipoProd)->where('tipo_tiempo_id', $tipoTiempo)->where('tipo_pago_id', $tipo_pago)->get();


    if ($tarifa->isEmpty()) { //Si el cliente no tiene tarifas, traemos las standar
        $tarifa = Tarifa::select('tipo_ciudad_id', 'valor')->whereraw('cantidad_desde_hasta @> ' . $cantidad)->where('tipo_mensajeria_id', $tipoMensajeria)->where('tipo_producto_id', $tipoProd)->where('tipo_tiempo_id', $tipoTiempo)->where('tipo_pago_id', $tipo_pago)->get();

        //dd($tarifa);

    }


    return $tarifa;
}

function ftarifas($tipo_pago, $tipoMensajeria, $tipoProd, $tipoTiempo, $tipoDest, $cantidad) {
    $tarifa = Tarifa::select('valor')->where('cantidad_desde_hasta', $cantidad)->where('tipo_mensajeria_id', $tipoMensajeria)->where('tipo_producto_id', $tipoProd)->where('tipo_tiempo_id', $tipoTiempo)->where('tipo_ciudad_id', $tipoDest)->where('tipo_pago_id', $tipo_pago)->first();

    if (!$tarifa) {
        $valor = 0;
    } else {
        $valor = $tarifa->valor;
    }

    return round($valor);
}

function fservicios($cliente_id, $servicio_id, $tipo) {
    $tarifa = ClientesServicio::select('id','valor')->where('cliente_id', $cliente_id)->where('servicio_id', $servicio_id)->first();

    if ($tipo==1) {
        if (!$tarifa) {
            return null; 
        } else {
            return $tarifa->id; 
        }
    } else {
        if (!$tarifa) {
            $tarifa = Servicio::select('valor')->findOrFail($servicio_id);
            $valor = round($tarifa->valor, 0);
        } else {
            $valor = round($tarifa->valor, 0);
        }
        return $valor;
    }
}

function fnombre_tercero($id) {
    $tercero = Tercero::select('nombres', 'apellidos')->find($id);
    return $tercero->nombres . " " . $tercero->apellidos;
}

function forden_servicios($orden_id, $padre_actual) {
    $servicios = OrdenesServicio::select('servicio_id')->with('servicio')->where('orden_id', $orden_id)->get();

    $return = "";
    $i      = 1;

    foreach ($servicios as $servicio) {
        $color = "tag label label-info";

        if ($padre_actual === $servicio->servicio->padre_id) {
            $color = "tag label label-success";
        }

        $return .= "<span class=' " . $color . "'>" . $servicio->servicio->nombre . "</span> ";

        if ($i % 3 === 0) {
            $return .= "<br>";
        }

        $i++;
    }

    return $return;
}

function forden_porcentaje($orden_id, $t) {
    $servicios = OrdenesResumen::select('cantidad', 'entregas', 'devoluciones', 'retenciones')->where('orden_id', $orden_id)->first();

    $proceso = 0;
    $return  = "";

    if (!empty($servicios)) {
        $proceso = ($servicios->entregas) + ($servicios->devoluciones) + ($servicios->retenciones);

        if (($servicios->cantidad) > 0) {
            $proceso = ($proceso / ($servicios->cantidad)) * 100;
        } else {
            $proceso = 0;
        }

    } else {

    }

    $proceso = round($proceso);

    if ($t === 0) {
        $return = $proceso;
    } else {
        $return = "<div class='progress-bar progress-bar-info progress-bar-striped active' role='progressbar' style='width: " . $proceso . "%;''>
                              " . $proceso . "%
                            </div>";
    }

    return $return;
}
