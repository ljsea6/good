@extends('templates.dash')

@section('titulo','Dashboard Recogidas')

@section('styles')
    {!! Html::style('css/fullcalendar.min.css') !!}
@endsection

@section('content')
<section class="invoice">
<div class="page-header no-breadcrumb font-header"><i class="fa fa-truck"></i> Rol Recogidas</div>  
 <div class="panel panel-default">
    <div class="panel-body">
      <ul class="nav nav-tabs font-12">
                <li role="presentation" class="active"><a href="#inicio" data-toggle="tab">Inicio</a></li>
                <li role="presentation"><a href="#cal" data-toggle="tab">Calendario Recogidas</a></li>
      </ul>
      <div class="tab-content b-all no-b-t p-20 font-12">
        <div class="tab-pane fade in active" id="inicio">
          <div class="row">
              <div class="col-lg-1 col-xs-8">
              </div>
          <div class="col-lg-3 col-xs-8">
            <div class="panel panel-default bg-info panel-stat no-icon">
                <div class="panel-body content-wrap">
                  <div class="value">
                    <h2 class="font-header no-m">{{ $sin_llegada[0]->count }}</h2>
                  </div>
                  <div class="detail text-right">
                    <div class="text-upper">Recogidas sin ingresar a bodega</div>
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
                    <small class="text-upper">Recogidas pendientes de ingresar dinero</small>
                    <div class="text-muted m-d-2">Con mas de 24 horas</div>
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
                    <div class="text-upper">Recogidas pendientes por asignar</div>
                    <div class="text-muted m-d-2">De los proximos dos dias</div>
                  </div>
                </div>
              </div>
          </div>
          <div class="col-lg-1 col-xs-8">
          </div>        
        </div>
        <div class="row">
         {!! Alert::render() !!}
         <div class="col-xs-12">
          <div class="panel">
            <table class="table table-bordered table-striped font-12">
                  <thead>
                      <tr>
                          <th>Recogida #</th>
                          <th>Fecha</th>
                          <th>Factura</th>
                          <th>Cliente</th>
                          <th>Accciones</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach ($recogidas as $recogida)
                        <tr>
                          <td>
                            {{ $recogida->numero }}
                          </td>
                          <td>
                            {{ date("d/m/Y", strtotime($recogida->fecha)) }}
                          </td>
                          <td>
                            <div align=center>
                               <a href="{{ route('admin.facturacion.imprimir', array($recogida->factura_id,0)) }}" target=_new>
                               Ver Factura
                             </a></div>
                          </td>
                          <td>
                            {{ $recogida->cliente->nombres }} {{ $recogida->cliente->apellidos }}
                          </td>
                            <td align="center">
                               <div align=center><a href="{{ route('admin.recogidas.ingresar_pago', $recogida->id) }}"  class="btn btn-warning btn-xs">
                                <span class="fa fa-arrow-down" aria-hidden="true"> Ingresar Pago</span>
                      </a></div>
                            </td>
                        </tr>
                      @endforeach
                  </tbody>
              </table>
               {!! $recogidas->render() !!}
            </div>
           </div>
           {!! Form::open(array('url' => 'admin/ordenes/llegada/update','class'=>'form-control'))!!}
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
                                  <label for='cliente'>Producto :</label>
                                  <div id='producto'></div>
                                </div>
                          </div>
                          <br><br>
                                <div class="row"> 
                                  <div class="col-xs-10">
                                    Marcar Ingreso Parcial
                                    <label class="switch-toggle">
                                      <input type="checkbox" name=parcial value=1>
                                      <span></span>
                                    </label>
                                  </div>
                                  <br><br>
                                </div>
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
                                <div class="row">
                                    <div class="col-xs-2">
                                        6
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
                                        7
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
                                        8
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
                                        9
                                    </div>                                 
                                    <div class="col-xs-4">
                                        <input type="number" class="form-control has-error" id="inicio[]" name="inicio[]" placeholder="Inicio" value="">
                                    </div>
                                    <div class="col-xs-4">
                                        <input type="number" class="form-control" id="fin[]" name="fin[] placeholder="Fin" value="">
                                    </div>
                                </div>                                                                
                                <div class="row">
                                    <div class="col-xs-2">
                                        10
                                    </div>                                 
                                    <div class="col-xs-4">
                                        <input type="number" class="form-control has-error" id="inicio[]" name="inicio[]" placeholder="Inicio" value="">
                                    </div>
                                    <div class="col-xs-4">
                                        <input type="number" class="form-control" id="fin[]" name="fin[]" placeholder="Fin" value="">
                                    </div>
                                </div>  
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="btn-save" value="add">Registrar Entrada a Bodega</button>
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
      <div class="tab-pane active" id="cal">
           <a href="{{ route('admin.recogidas.asignar') }}" type="submit" class="btn btn-primary" id="btn-save" value="add" align=center>Recogidas programadas pendientes por asignar mensajero</a>
           <br><br>
          <div id="fullCalendar"></div>
      </div>

    </div>
  </div>
  {!! Form::open(['route' => ['admin.recogidas.asignar3'] , 'method' => 'POST' ]) !!}
  <div class="modal fade" id="updateEventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="updateEvent"></h4>
                    </div>
                    <div class="modal-body">
                              <div class="row" id='mensajero_c'>
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
                                                            <textarea id=obs name=obs class="form-control" rows="3"></textarea>
                                                          </div>
                                                        </div>
                                                    </div>
                                                </li>
                                              </ul>
                                            </div>
                                          </div>
                                  </div> 
                              </div>
                            <br><br><br>
                    </div>
                     <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="btn-save" value="add">No efectiva</button>
                            <input type="hidden" id="recogida_id" name="recogida_id" value="0">
                     </div>
                </div>
            </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@section('scripts')
  {!! Html::script('js/fullcalendar.min.js') !!}
  {!! Html::script('js/calendar_es.js') !!}
	<script type="text/javascript" src="{{ asset('js/Chart.min.js') }}"></script>
  <script type="text/javascript">
  $(function()  {
   //$('#mensajero').select2({
   //             language: "es",
   //             placeholder: "Seleccionar cliente...",
   //         });
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
        }) 
    });

    $('#fullCalendar').fullCalendar({
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,basicDay'
          },
          lang: 'es',
          defaultDate: '{{ date("Y-m-d") }}',
          editable: false,
          businessHours: true, // display business hours
          selectable: true,
          selectHelper: true,
          select: function(start, end) {
            //eventStart = start;
            //eventEnd = end;
            //$('#addEventModal').modal('show');
          },
          eventClick: function(event, element) {
            selectedEvent = event;
            
            var t = selectedEvent.title.split("|");

            $('#recogida_id').val(t[1]);
            $('#obs').val('');
            $('#updateEvent').text(selectedEvent.title);
            $('#updateEventModal').modal('show');
          },
          //eventLimit: true, // allow "more" link when too many events
          events: {
              url: '{{ route('admin.calendario.recogidas') }}',
              type: 'POST',
              data: {
                  custom_param1: 'something',
                  custom_param2: 'somethingelse'
              },
              error: function() {
                  alert('there was an error while fetching events!');
              },
              //color: 'yellow',   // a non-ajax option
              //textColor: 'black' // a non-ajax option
          }
        });
      });

  </script>
@stop