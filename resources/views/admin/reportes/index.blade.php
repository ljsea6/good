@extends('templates.dash')

@section('titulo', 'Recogidas')

@section('content')
<div class="search-result-wrap">
            <div class="row">
              <div class="col-md-4 col-lg-3 form-horizontal font-12"  id="params" >
                <div class="search-filter-wrap">
                  <div class="row">
                    <div class="col-sm-4 col-md-12 col-lg-12">
                      <div class="search-result-filter">
                        <div class="filter-heading font-header">
                          Reportes
                          <div class="pull-right m-d-2"><span data-target="#catFilterWidget" class="expand-widget text-muted text-action" data-toggle="collapse" aria-expanded="true" role="button"><i class="icon-circle-up"></i></span></div>
                        </div>
                        <div class="filter-body collapse in" id="catFilterWidget">
                          <div class="content-wrap">
                                  <div class="form-group has-feedback row">
                                    <div class="col-xs-6">
                                      <input type="text" class="form-control font-12" id="desde"/ placeholder="Desde">
                                      <span class="icon-calendar h4 no-m form-control-feedback" aria-hidden="true"></span>
                                    </div>
                                    <div class="col-sm-6">
                                      <input type="text" class="form-control font-12" id="hasta"/ placeholder="Hasta">
                                      <span class="icon-calendar h4 no-m form-control-feedback" aria-hidden="true"></span>
                                    </div>
                                  </div>
                                  <div class="form-group has-feedback row">
                                      <div class="col-sm-12">
                                        <select id='reporte' class="form-control">
                                          <option value=''>Elija tipo de reporte</option>
                                          <option value=1>Por Orden</option>
                                          <option value=2>Por Cliente</option>
                                          <option value=3>Por Destino</option>
                                          <option value=4>Por Courier</option>
                                        </select>
                                      </div>
                                  </div>
                            </div>
                        </div>
                      </div><!-- /.search-result-filter -->
                    </div><!-- /.col -->
                     <div class="col-sm-4 col-md-12 col-lg-12">
                      <div class="search-result-filter ">
                        <div class="filter-heading font-header">
                          Filtros
                          <div class="pull-right m-d-2"><span data-target="#priceFilterWidget" class="expand-widget text-muted text-action" data-toggle="collapse" aria-expanded="true" role="button"><i class="icon-circle-up"></i></span></div>
                        </div>
                        <div class="filter-body collapse in" id="priceFilterWidget">
                          <div class="content-wrap">
                                {!! Field::select('cliente',$clientes,null, ['class'=>'form-control chosen-select font-12'] ) !!}
                                <label for="orden">No. Orden</label>
                                <input type="text" class="form-control" id="orden" placeholder="# Orden">
                          </div>
                        </div>
                      </div><!-- /.search-result-filter -->
                    </div> 
                    <div class="col-sm-offset-3 col-sm-9"">
                        <button type="button" class="btn btn-main" id="reporte_buttom">Generar Reporte</button>
                    </div>
                  </div><!-- /.row -->
                </div><!-- /.search-filter-wrap -->
              </div><!-- /.col -->
              <div class="col-md-8 col-lg-9" >
                   <div class="row">
                          <div class="panel panel-default">
                            <div class="panel-heading font-header">Resultados</div>
                            <div id="tabla_envios_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                <div id="processing" class="dataTables_processing panel panel-default" style="display: none;">Procesando...</div>
                                <div id="resultado">
                                   
                                </div>
                            </div>
                          </div>
                  </div>
              </div>
          </div>
@stop

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#desde').datetimepicker({  format: 'YYYY-MM-DD' });
            $('#hasta').datetimepicker({  format: 'YYYY-MM-DD' });
            $('#cliente').select2({
                language: "es",
                placeholder: "Seleccionar cliente...",
                maximumSelectionLength: 5
            });

            $("#reporte_buttom").click(function() {
                 if (!$("#desde").val()) {
                    alert('Elija la fecha inicial por favor');
                    return false;
                 }
                 if (!$("#hasta").val()) {
                    alert('Elija la fecha final por favor');
                    return false;
                 }
                 if (!$("#reporte").val()) {
                    alert('Elija el tipo de reporte por favor');
                    return false;
                 }
                 $('#processing').show();
                 //$('#params').hide();               
                 $.post('{{ route('admin.reportes.datos') }}', { desde: $("#desde").val(), hasta: $("#hasta").val(), reporte: $("#reporte").val(), cliente: $("#cliente").val(), orden: $("#orden").val()} , function(data) {
                    $("#resultado").html(data);
                    $('#processing').hide();
                });
            });
        });        
    </script>

@endpush