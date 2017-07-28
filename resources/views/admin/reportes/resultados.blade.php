<!--  <button type="button" class="btn btn-main center" id="reporte_buttom" onclick=" $('#params').show(); ">Cambiar parametros</button>
 -->
  <table class="table table-bordered font-12" id="resultado">
        <thead>
          @if ($req->reporte ==1 ) 
             <tr><th rowspan=2>Orden</th>
          @endif
          @if ($req->reporte ==2 ) 
             <tr><th rowspan=2>Cliente</th>
          @endif
          @if ($req->reporte ==3 ) 
             <tr><th rowspan=2>Destino</th>
          @endif         
          @if ($req->reporte ==4 ) 
             <tr><th rowspan=2>Courier</th>
          @endif 
          <th rowspan=2>Cantidad</th>
          <th colspan="{{ ($entregas->count()+1) }}">Entregas</th>
          <th colspan="{{ ($devoluciones->count()+1) }}">Devoluciones</th></tr>
          <tr>
             <th>Total</th>
             @foreach ($entregas as $ent)
                <td align="center">
                    <span class="tooltip-success" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $ent->nombre }}">{{ $ent->alias }}</button>
                </td>
             @endforeach
             <th>Total</th>
             @foreach ($devoluciones as $dev)
                <td align="center">
                    <span class="tooltip-success" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $dev->nombre }}">{{ $dev->alias }}</span>
                </td> 
              @endforeach
             <th rowspan=2>En Proceso</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($resumenes as $resumen)
              <tr>          
                <td>
                  @if ($req->reporte == 1) 
                    {{ $resumen->orden->numero }} <!-- ( {{ $resumen->orden->producto->nombre }}) --><br>
                    {{ $resumen->orden->cliente->nombres }} {{ $resumen->orden->cliente->apellidos }}
                  @endif
                  @if ($req->reporte == 2) 
                    {{ fnombre_tercero($resumen->cliente_id) }}
                  @endif          
                  @if ($req->reporte == 3) 
                    {{ $resumen->destino->nombre }}
                  @endif
                  @if ($req->reporte == 4) 
                    {{ fnombre_tercero($resumen->courier_id) }}
                  @endif
                </td>
                <td align=right>
                    <a href="{{ route('admin.reportes.descargar', array('orden_id'=>$resumen->orden_id,'desde'=>$req->desde,'hasta'=>$req->hasta,'padre_id'=>1)) }}" class="btn btn-link">{{ number_format($resumen->cantidad) }}
                </td>
                <td align=right>
                    <a href="{{ route('admin.reportes.descargar', array('orden_id'=>$resumen->orden_id,'desde'=>$req->desde,'hasta'=>$req->hasta,'padre_id'=>2)) }}" class="btn btn-link">
                      {{ number_format($resumen->entregas) }}
                    </a>
                </td>
                @foreach ($entregas as $ent)
                  <td align=right>
                    @foreach ($resumen->detalle as $detalle)
                       @if ($detalle->estado_id == $ent->id) 
                            <a href="{{ route('admin.reportes.descargar', array('orden_id'=>$resumen->orden_id,'desde'=>$req->desde,'hasta'=>$req->hasta,'estado_id'=> '')) }}" class="btn btn-link">
                              {{ number_format($detalle->cantidad) }}
                            </a>
                       @endif
                    @endforeach
                  </td>
                @endforeach
                <td align=right>
                    <a href="{{ route('admin.reportes.descargar', array('orden_id'=>$resumen->orden_id,'desde'=>$req->desde,'hasta'=>$req->hasta,'padre_id'=>3)) }}" class="btn btn-link">
                      {{ number_format($resumen->devoluciones) }}
                    </a>
                </td>
                @foreach ($devoluciones as $dev)
                  <td align=right>
                    @foreach ($resumen->detalle as $detalle)
                      @if ($detalle->estado_id == $dev->id) 
                            <a href="{{ route('admin.reportes.descargar', array('orden_id'=>$resumen->orden_id,'desde'=>$req->desde,'hasta'=>$req->hasta,'estado_id'=> '')) }}" class="btn btn-link">
                              {{ number_format($detalle->cantidad) }}
                            </a>
                      @endif
                    @endforeach
                  </td>
                @endforeach
                <td align=right>
                  <a href="{{ route('admin.reportes.descargar', array('orden_id'=>$resumen->orden_id,'desde'=>$req->desde,'hasta'=>$req->hasta,'padre_id'=>1)) }}" class="btn btn-link">
                    {{ number_format(($resumen->cantidad)-($resumen->entregas)-($resumen->devoluciones)-($resumen->retenciones)) }}
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
             <th colspan=20>{{ $resumenes->count(0) }} Ordenes</th>
          </tfoot>
</table>
{!! $resumenes->render() !!}
{!! Html::script('js/app.min.js') !!}
