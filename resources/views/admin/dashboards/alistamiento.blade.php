@extends('templates.dash')

@section('titulo','Dashboard Alistamiento')

@section('content')
<section class="invoice">
<div class="page-header no-breadcrumb font-header"><i class="fa fa-truck"></i> Rol Alistamiento</div>  
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
                    <h2 class="font-header no-m">{{ $sin_llegada[0]->count }}</h2>
                  </div>
                  <div class="detail text-right">
                    <div class="text-upper">Ordenes pendientes por alistar</div>
                    <div class="text-muted m-d-2">Con mas de 24 horas</div>
                  </div>
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
                    <h2 class="font-header no-m">{{ $sin_facturar[0]->count }}</h2>
                    <small class="text-upper">Ordenes por facturar</small>
                  </div>
                </div>
              </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-8">
              <div class="panel panel-default bg-purple panel-stat no-icon">
                <div class="panel-body content-wrap">
                  <div class="value">
                    <h2 class="font-header no-m">{{ $sin_cerrar[0]->count }}</h2>
                  </div>
                  <div class="detail text-right">
                    <div class="text-upper">Ordenes por cerrar</div>
                    <div class="text-muted m-d-2">Con mas de 72 horas</div>
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
                          <th style="text-align: center;">Acciones</th>
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
                              <?echo forden_servicios($orden->id,5)?>
                          </td>
                          <td>
                              <?=forden_porcentaje($orden->id,1)?>
                          </td>
                          <td>
                              <div align=center><?=forden_porcentaje($orden->id,0)?>%</div>
                          </td>
                            <td align="center"> 
                                @if (!$orden->alistamiento_inicio) 
                                    <button class="btn btn-primary btn-xs btn-detail open-modal" value="{{$orden->id}}">Registrar Entrada Alistamiento</button>
                                @else 
                                    <i>Inicio {{ date("d/m/Y", strtotime($orden->alistamiento_inicio))  }}</i><br>
                                    <button class="btn btn-primary btn-xs btn-detail open-modal" value="{{$orden->id}}">Registrar Salida   Alistamiento</button>
                                @endif 
                                  
                            </td>
                        </tr>
                      @endforeach
                  </tbody>
              </table>
               {!! $ordenes->render() !!}
            </div>
          </div>
          {!! Form::open(array('url' => 'admin/ordenes/alistamiento/update','class'=>'form-control'))!!}
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
                            <button type="submit" class="btn btn-primary" id="btn-save" value="add">Registrar Inicio Alistamiento</button>
                            <input type="hidden" id="orden_id" name="orden_id" value="0">
                            <input type="hidden" id="salida" name="salida" value="0">
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
            if (data.alistamiento_inicio != null) {
                //alert(data.alistamiento_inicio);
                $('#btn-save').html('Registrar Salida Alistamiento');
            } else {
                $('#btn-save').html('Registrar Inicio Alistamiento');
            }
            var url = "admin/ordenes/obs/";
            $.get(url + data.id, function (data2) {
                 $.each(data2, function(index) {
                    //console.log(data2[index]);
                    $('#obs').append("<li><div class='user-wrapper'><div class='profile-pic'><img src='"+data2[index].avatar+"' alt='' class='img-circle'></div><div class='p-t-5'><div class='font-12 text-dark'>"+data2[index].nombres+" "+data2[index].apellidos+"</div><div class='font-10 text-ellipsis'>"+data2[index].obs+"</div></div></div></li><br>");
                });
            });
        }) 
    });
    
    $('.open-salida').click(function(){

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
              $("#reporte").val(3);
              $("#title_reporte").html("Ordenes pendientes por alistar con mas de 24 horas");
              tabla.ajax.reload();
    });
    var tabla = $('#tabla_ordenes').DataTable({
                  processing: true,
                  serverSide: true,
                  deferRender: true,
                  pagingType: "full_numbers",
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
    // var mainColor = '#5090F7';
    //     var secondaryColor = '#34495e';
    // ////initialize all charts
    //     initChart();

    //     function initChart()  {        
    //       var data = [{ data: [[0, 5000], [1, 8000], [2, 5000], [3, 8000], [4, 7000], [5,9000], [6, 8000], [7, 8000], [8, 10000], [9, 12000], [10, 10000], [11, 5000], [12, 9000]],
    //         label: "Costo de ordenes"
    //       },
    //         { data: [[0, 8000], [1, 9000], [2, 12000], [3, 11000], [4, 13000], [5,9000], [6, 10000], [7, 9000], [8, 9000], [9, 8000], [10, 10000], [11, 12000], [12, 13000 ]],
    //         label: "Valor facturado de ordenes"
    //       }],

    //       options = { 
    //         xaxis: {
    //           ticks: [[1, 'Jan'], [2, 'Feb'], [3, 'Mar'], [4, 'Apr'], [5, 'May'], [6, 'Jun'], [7, 'Jul'], [8, 'Aug'], [9, 'Sep'], [10, 'Oct'], [11, 'Nov'], [12, 'Dec']]
    //         },
    //         series: {
    //           lines: {
    //             show: true, 
    //           },
    //           points: {
    //             show: true,
    //             radius: '3.5'
    //           },
    //           shadowSize: 0
    //         },
    //         grid: {
    //           hoverable: true,
    //           clickable: true,
    //           color: '#bbb',
    //           borderWidth: 1,
    //           borderColor: '#eee'
    //         },
    //         colors: [mainColor,secondaryColor]
    //       },
    //       plot;

    //       //Area Chart
    //       var areaData = [{ 
    //         data: [[0, 5], [1, 8], [2, 5], [3, 8], [4, 7], [5,9], [6, 8]],
    //           label: "Promedio Ordenes Cerradas ultimos 5 dias"
    //         }
    //       ],

    //       areaOptions = { 
    //         xaxis: {
    //           ticks: [[1, 'Mon'], [2, 'Tue'], [3, 'Wed'], [4, 'Thr'], [5, 'Fri'], [6, 'Sat'], [7, 'Sun']]
    //         },
    //         series: {
    //           lines: { 
    //             show: true, 
    //             fill: true
    //           },
    //           shadowSize: 0
    //         },
    //         grid: {
    //           hoverable: true,
    //           clickable: true,
    //           color: '#bbb',
    //           borderWidth: 1,
    //           borderColor: '#eee'
    //         },
    //         colors: [mainColor]
    //       },
    //       areaPlot;

    //       plot = $.plot($('.line-placeholder'), data, options);
    //       //areaPlot = $.plot($('.area-placeholder'), areaData, areaOptions);

    //       $("<div id='tooltip'></div>").css({
    //         position: "absolute",
    //         display: "none",
    //         border: "1px solid #95a4b8",
    //         padding: "4px",
    //         "font-size": "12px",
    //         color: "#fff",
    //         "border-radius": "4px",
    //         "background-color": "#95a4b8",
    //         opacity: 0.90
    //       }).appendTo("body");

    //       $(".chart-placeholder").bind("plothover", function (event, pos, item) {
    //         var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
    //         $("#hoverdata").text(str);
          
    //         if (item) {
    //           var x = item.datapoint[0],
    //             y = item.datapoint[1];
              
    //             $("#tooltip").html(item.series.label+ " : " + y)
    //             .css({top: item.pageY+5, left: item.pageX+5})
    //             .fadeIn(200);
    //         } else {
    //           $("#tooltip").hide();
    //         }
    //       });

    //       //Sparkline
    //       // $('.sparkbar').sparkline([15,19,20,22,33,27,31,27,19,30,21,10,15,18,25,9,11,14,16,10,15,16], {
    //       //   type: 'bar', 
    //       //   barColor: mainColor
    //       // });
    //       // $('.sparkline').sparkline([15,19,20,22,33,27,31,27,19,30,21,10,15,18,25,9,11,14,16,10,15,16,7,12,16,17,14,9,2,12,15,16,7,12,15,12,9,5,7,19,11,20,12,12], {
    //       //   type: 'line', 
    //       //   lineColor: mainColor,
    //       //   fillColor: false,
    //       //   maxSpotColor: false,
    //       //   minSpotColor: false,
    //       //   spotRadius: 0,
    //       //   highlightLineColor: secondaryColor
    //       // });
    //     }
  </script>>
@stop