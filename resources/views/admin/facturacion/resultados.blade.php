@extends('templates.dash')

@section('titulo','Crear Factura')


@section('content')
<div class="panel panel-default">
  <div class="panel-body">
    <div class="box box-primary form-horizontal font-12">
      <div class="box-header with-border">
            <h3 class="box-title">Crear facturación para clientes credito :</h3>
      </div>
      <div class="box-body">
      {!! Form::open(['route' => ['admin.facturacion.store'] , 'method' => 'POST' ]) !!}
      <div class="col-xs-9">
        <table class="table table-bordered font-12" id="resultado">
        <thead>
          <tr>
          <th rowspan="2">Orden</th>
          <th rowspan="2">Cliente</th>
          <th rowspan="2">Producto</th>
          <th rowspan="2">Tipo Mensajeria</th>
          <th rowspan="2">Tipo Envio</th>
          <th rowspan="2">Tipo Tiempo</th>
          <th rowspan="2">Tipo Destino</th>
          <th colspan=2>Entregas</th>
          <th colspan=2>Devoluciones</th>
          <th rowspan="2">Total</th>
        </tr>
          <tr>
            <td>Cantidad</td>
            <td>Valor</td>
            <td>Cantidad</td>
            <td>Valor</td>
          </tr>
         </thead>
         <tbody>
            @foreach ($resumenes as $resumen)
              <tr>
                <td>
                  {{ number_format($resumen->orden->numero) }}
                </td>
                <td>{{ $resumen->orden->cliente->nombres }} {{ $resumen->orden->cliente->apellidos }}</td>
                <td>{{ $resumen->orden->producto->nombre }}</td>
                <td align="center">Masivos</td>
                <td align="center">Sobres</td>
                <td align="center">Normal</td>
                <td  align="center">{{ $resumen->nombre }}</td>
                <td align=right>
                      {{ $entregas=number_format($resumen->entregas) }}
                </td>
                <td align=right>
                      {{ $vr_entregas=ftarifas_tercero_facturacion($resumen->orden->cliente->id,1,7,24,13,$resumen->id,15)*$entregas }}
                </td>
                <td align=right>
                      {{ $devoluciones=number_format($resumen->devoluciones) }}
                </td>
                <td align=right>
                      {{ $vr_devoluciones=number_format(ftarifas_tercero_facturacion($resumen->orden->cliente->id,1,7,24,13,$resumen->id,15)*$devoluciones) }}
                </td>
                <th align=right>$ {{ number_format($vr_entregas+$vr_devoluciones) }}</th>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
             <th colspan=10><br><br> {!! Form::submit('Generar Facturaci&oacute;n', [ 'class' => 'btn btn-success btn-lg guardar','id' => 'guardar']) !!}</th>
          </tfoot>
          </table>
          {!! $resumenes->render() !!}
        </div>
       </div>
        {!! Field::hidden('desde',$req->desde) !!}
        {!! Field::hidden('hasta',$req->hasta) !!}
        {!! Field::hidden('cliente',$req->cliente) !!}
        {!! Form::close() !!}
    </div>
</div>
@endsection