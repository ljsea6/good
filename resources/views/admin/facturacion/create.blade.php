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
            {!! Form::open(['route' => ['admin.facturacion.buscar'] , 'method' => 'POST' ]) !!}

              <div class="form-group has-feedback row">
                <div class="col-md-3">
                  <input type="text" class="form-control font-12" name="desde" id="desde"/ placeholder="Desde">
                  <span class="icon-calendar h4 no-m form-control-feedback" aria-hidden="true"></span>
                </div>
                <div class="col-md-3">
                  <input type="text" class="form-control font-12" name="hasta" id="hasta"/ placeholder="Hasta">
                  <span class="icon-calendar h4 no-m form-control-feedback" aria-hidden="true"></span>
                </div>
              </div>            
              <div class="col-md-6">
                {!! Field::select('cliente', $clientes, ['class'=>'form-control chosen-select font-12']) !!}
                {!! Form::submit('Enviar', [ 'class' => 'btn btn-success btn-lg pull-right guardar','id' => 'guardar']) !!}
               </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

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
        });
    </script>
@endpush