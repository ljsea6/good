@extends('templates.dash')

@section('titulo','Dashboard Impresion')

@section('content')
<section class="invoice">
<div class="page-header no-breadcrumb font-header"><i class="fa fa-truck"></i> Rol Impresion</div>  
 <div class="panel panel-default">
    <div class="panel-body">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
        </div>
        <!-- /.col -->
      </div>
      <div class="row">
        <div class="col-lg-1 col-xs-8">
        </div>
        <div class="col-lg-3 col-xs-8">
          <div class="panel panel-default bg-info panel-stat no-icon open-alerts" data-toggle="modal" data-target=".modal-flip-vertical">
                <div class="panel-body content-wrap">
                  <div class="value">
                    <h2 class="font-header no-m ">{{ $sin_llegada[0]->count }}</h2>
                  </div>
                  <div class="detail text-right">
                    <div class="text-upper">Ordenes pendientes por imprimir</div>
                    <div class="text-muted m-d-2">Con mas de 24 horas</div>
                  </div>
                </div>
          </div>
        </div>
        <div class="modal modal-flip-vertical fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id='title_reporte'>Listado de Ordenes</h4>
                </div>
                <div class="modal-body">
                    <table id="tabla_ordenes" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info" style="width: 100%">
                          <thead>
                              <tr>
                                <th>Orden</th>
                                <th>Fecha</th>
                                <th>Forma Pago</th>
                                <th>Cliente</th>
                              </tr>
                          </thead>
                      </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-gray" data-dismiss="modal">Close</button>
                </div>
                <input type=hidden id=reporte value=0>
              </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-8">
              <div class="panel panel-default bg-success panel-stat">
                <div class="panel-body">
                  <div class="value stat-icon">
                    <span><i class="icon-upload"></i></span>
                  </div>
                  <div class="detail text-right">
                    <h2 class="font-header no-m">{{ $sin_digitar[0]->count }}</h2>
                    <small class="text-upper">Ordenes por digitar</small>
                    <div class="text-muted m-d-2">Con mas de 24 horas</div>
                  </div>
                </div>
              </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-8">
              <div class="panel panel-default bg-purple panel-stat no-icon open-sinbase" data-toggle="modal" data-target=".modal-flip-vertical">
                <div class="panel-body content-wrap">
                  <div class="value">
                    <h2 class="font-header no-m">{{ $sin_base[0]->count }}</h2>
                  </div>
                  <div class="detail text-right">
                    <div class="text-upper">Ordenes por procesar Base</div>
                    <div class="text-muted m-d-2">Con mas de 24 horas</div>
                  </div>
                </div>
              </div>
        </div>
        <div class="col-lg-1 col-xs-8">
        </div>        
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="panel">
            <table class="table table-bordered table-striped font-12">
                  <thead>
                      <tr>
                          <th>Numero</th>
                          <th>Fecha</th>
                          <th>Cliente/Producto</th>
                          <th>Servicios</th>
                          <th>Proceso</th>
                          <th style="text-align: center;">%</th>
                          <th style="text-align: center;">Accciones</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach ($ordenes as $orden)
                        <tr>
                          <td>
                            {{ $orden->numero }}
                          </td>
                          <td>
                            {{ date("d/m/Y", strtotime($orden->fecha)) }}
                          </td>
                          <td>
                            {{ $orden->cliente->nombres }} {{ $orden->cliente->apellidos }}<br>
                            {{ $orden->producto->nombre }}
                          </td>
                          <td>
                              <?echo forden_servicios($orden->id,4)?>
                          </td>
                          <td>
                              <?=forden_porcentaje($orden->id,1)?>
                          </td>
                          <td>
                              <div align=center><?=forden_porcentaje($orden->id,0)?>%</div>
                          </td>
                          <td align="center">
                               <button class="btn btn-primary btn-xs btn-detail open-modal" value="{{$orden->id}}">Registrar Impresi&oacute;n</button>
                          </td>
                        </tr>
                      @endforeach
                  </tbody>
              </table>
               {!! $ordenes->render() !!}
            </div>
          </div>
          {!! Form::open(array('url' => 'admin/ordenes/impresion/update','class'=>'form-control'))!!}
          <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalLabel"><div id='numero'>Orden # : </div></h4>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                               <div class="col-xs-6">
                                  <label for='cliente'>Cliente :</label>
                                  <div  id='cliente'></div>
                               </div>
                               <div class="col-xs-6">
                                  <label for='producto'>Producto :</label>
                                  <div id='producto'></div>
                                </div>
                            </div><br><br>
                            <div class=row>
                                    <div class="col-xs-3">Cantidad Ingresada : </div>
                                    <div class="col-xs-3"><input type="number" name="cantidad" value="0" class="form-control" style="text-align: right;">
                                    </div>
                                    <div class="col-xs-6">
                                      <div class="radio">
                                        <div class="custom-radio font-12">
                                          <input id="radio1" type="radio" name="parcial" checked="" value=1 OnChange=" $('#rangos').show();">
                                          <label for="radio1">Ingreso Parcial</label>
                                        </div>
                                        <div class="custom-radio font-12">
                                          <input id="radio2" type="radio" name="parcial" checked="" value=0  OnChange="$('#rangos').hide();"> 
                                          <label for="radio2">Ingreso Completa</label>
                                        </div>
                                      </div>
                                    </div>
                               </div>
                                <div id='rangos' style="display: none;">
                                  
                                  Rangos de envios :<br><br>
                                   <div class="row">
                                      <div class="col-xs-2">
                                          1
                                      </div>                                 
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control has-error" id="inicio[]" name="inicio[]" placeholder="Inicio" value="">
                                      </div>
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control" id="fin[]" name="fin[]" placeholder="Fin" value="">
                                      </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-xs-2">
                                          2
                                      </div>                                 
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control has-error" id="inicio[]" name="inicio[]" placeholder="Inicio" value="">
                                      </div>
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control" id="fin[]" name="fin[]" placeholder="Fin" value="">
                                      </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-xs-2">
                                          3
                                      </div>                                 
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control has-error" id="inicio[]" name="inicio[]" placeholder="Inicio" value="">
                                      </div>
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control" id="fin[]" name="fin[]" placeholder="Fin" value="">
                                      </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-xs-2">
                                          4
                                      </div>                                 
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control has-error" id="inicio[]" name="inicio[]" placeholder="Inicio" value="">
                                      </div>
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control" id="fin[]" name="fin[]" placeholder="Fin" value="">
                                      </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-xs-2">
                                          5
                                      </div>                                 
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control has-error" id="inicio[]" name="inicio[]" placeholder="Inicio" value="">
                                      </div>
                                      <div class="col-xs-4">
                                          <input type="number" class="form-control" id="fin[]" name="fin[]" placeholder="Fin" value="">
                                      </div>
                                  </div>
                                  <div id='nuevo_rango'></div>
                                  <div class="row">
                                    <div class="col-xs-8">
                                    </div>
                                    <div class="col-xs-4"> 
                                       <br><a class="btn btn-primary btn-xs btn-detail agregar_rango"">Agregar</a>
                                    </div>
                                  </div>
                                </div> 
                                <div class="panel">
                                          Observaciones :
                                          <div id="collapsedPanel3" class="collapse in">
                                            <div class="panel-body no-p-t">
                                              <ul class="activity-widget list-unstyled no-m">
                                                <div id='obs'></div>
                                                <br>
                                                <li>
                                                    <div class="user-wrapper">
                                                        <div class="profile-pic">
                                                          @if (currentUser()->avatar !== NULL)
                                                              <img src="{{ asset(currentUser()->avatar) }}" class="hidden-sm" alt="{{ currentUser()->nombre_completo }}"/>
                                                          @else
                                                              <img src="{{ asset("img/avatar-bg.png") }}" class="hidden-sm" alt="{{ currentUser()->nombre_completo }}"/>
                                                          @endif
                                                        </div>
                                                        <div class="p-t-5">
                                                          <div class="font-12 text-dark">{{ currentUser()->nombre_completo }}</div>
                                                          <div class="font-10 text-ellipsis">
                                                            <textarea name=obs class="form-control" rows="3"></textarea>
                                                          </div>
                                                        </div>
                                                    </div>
                                                </li>
                                              </ul>
                                            </div>
                                          </div>
                                  </div> 
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="btn-save" value="add">Registrar Inicio Impresi&oacute;n</button>
                            <input type="hidden" id="orden_id" name="orden_id" value="0">
                        </div>
                        </form>
                    </div>
                </div>
            </div>
          {!! Form::close() !!}
<!--           <div class="col-xs-5">
              <div class="panel-body">
                  <div class="row font-12">
                    <div class="col-xs-9">
                      <span class="text-upper font-header">Costo vs Facturación</span>
                      <span class="pointer m-d-1 m-l-10"><i class="icon-file-text2"></i></span>
                      <span class="pointer m-d-1 m-l-10 refresh-widget"><i class="icon-spinner9"></i></span>
                    </div>
                    <div class="col-xs-3 text-right">
                      <label class="switch-toggle m-l-5 switch-red">
                        <input type="checkbox">
                        <span></span>
                      </label>
                    </div>
                  </div>
                  <div class="chart-placeholder line-placeholder m-t-10"></div>
              </div>
          </div> -->
      </div>
  </div>
</section>
@endsection

@section('scripts')
	<script type="text/javascript" src="{{ asset('js/Chart.min.js') }}"></script>
<!-- 	<script type="text/javascript" src="{{ asset('js/dashboard2.js') }}"></script>
 --> 

 <script type="text/javascript">
    $(document).ready(function() { 
       var rango=6;
        $('.agregar_rango').click(function(){
           $('#nuevo_rango').append('<div class="row"><div class="col-xs-2">'+rango+'</div><div class="col-xs-4"><input type="number" class="form-control has-error" id="inicio[]" name="inicio[]" placeholder="Inicio" value=""></div><div class="col-xs-4"><input type="number" class="form-control" id="fin[]" name="fin[]" placeholder="Fin" value=""></div></div>');
           i++;
        });

       $('.open-modal').click(function(){

            var url = "admin/ordenes/llegada/";

            var task_id = $(this).val();

            $.get(url + task_id, function (data) {
                //success data
                //console.log(data);
                $('#orden_id').val(data.id);
                $('#numero').html('Orden # ' + data.numero);
                $('#cliente').html(data.cliente.nombres + data.cliente.apellidos);
                $('#producto').html(data.producto.nombre);
                $('#myModal').modal('show');
                $('#obs').html("");
                var url = "admin/ordenes/obs/";
                $.get(url + data.id, function (data2) {
                     $.each(data2, function(index) {
                        //console.log(data2[index]);
                        $('#obs').append("<li><div class='user-wrapper'><div class='profile-pic'><img src='"+data2[index].avatar+"' alt='' class='img-circle'></div><div class='p-t-5'><div class='font-12 text-dark'>"+data2[index].nombres+" "+data2[index].apellidos+"</div><div class='font-10 text-ellipsis'>"+data2[index].obs+"</div></div></div></li><br>");
                    });
                });
            }) 
        });
        $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
            });
        $('.open-alerts').click(function(){
              $("#reporte").val(1);
              $("#title_reporte").html("Ordenes pendientes por imprimir con mas de 24 horas");
              tabla.ajax.reload();
            });
        $('.open-sinbase').click(function(){
              $("#reporte").val(2);
              $("#title_reporte").html("Ordenes pendientes por procesar base con mas de 24 horas");
              tabla.ajax.reload();
            });
        var tabla = $('#tabla_ordenes').DataTable({
                  processing: true,
                  serverSide: true,
                  deferRender: true,
                  pagingType: "full_numbers",
                  dom: 'Bfrtip',
                  filename: 'Ordenes_pendientes',
                  buttons: [
                      'copy', 'csv', 'excel', 'pdf', 'print'
                  ],
                  ajax: {
                    'url' : '{!! route('ordenes.listar') !!}',
                    'data' : function ( d ) {
                            d.reporte=$("#reporte").val()
                     }
                  },
                  columns: [
                      { data: 'numero', name: 'numero'},
                      { data: 'fecha', name: 'fecha' },
                      { data: 'forma_pago', label: 'forma_pago' },
                      {
                          data: null, label: 'cliente', name: 'cliente.nombres',
                          render: function ( data, type, row ) {
                              // Combine the first and last names into a single table field
                              return data.cliente.nombres+' '+data.cliente.apellidos;
                          },
                          editField: ['nombres', 'apellidos']
                      },
                  ],
                  "language": {
                      "url": "{{ asset('css/Spanish.json') }}"
                  }
        });
      });
  </script>
@stop