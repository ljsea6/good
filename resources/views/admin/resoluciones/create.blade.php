@extends('templates.dash')

@section('titulo','Crear Resoluciones')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
    <div class="box-header with-border">
        <h3 class="box-title">Crear resolucion</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::open(['route' => ['admin.resoluciones.store'] , 'method' => 'POST' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::text('prefijo') !!}
                {!! Field::number('digitos', ['ph' => 'Cantidad Digitos','required']) !!}
                {!! Field::number('numero', ['ph' => 'Numero','required']) !!}
                {!! Field::number('desde', ['ph' => 'Desde','required']) !!}
                {!! Field::number('hasta', ['ph' => 'Hasta','required']) !!}
                <div class="row">
                    <div class='col-sm-6'>
                        <div class="form-group">
                            <div class='input-group date'>
                                {!! Field::text('vencimiento', ['ph' => 'Vencimiento','required']) !!}
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#vencimiento').datetimepicker({  format: 'YYYY-MM-DD' });
        });
    </script>
@endpush