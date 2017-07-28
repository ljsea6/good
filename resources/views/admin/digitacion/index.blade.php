@extends('templates.dash')

@section('titulo','Digitacion')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <h3 class="box-title">Digitar guia manualmente</h3>
        {!! Alert::render() !!}
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <div class="box-body">
        {!! Form::open(['route' => ['admin.digitacion.store'] , 'method' => 'POST' ]) !!}
        <div class="row">
            <div class="col-xs-3">
                {!! Field::number('orden', ['ph' => 'Orden #','required']) !!}
            </div>            
            <div class="col-xs-3">
                {!! Field::number('idenvio', ['ph' => 'idenvio','required']) !!}
            </div>
            <div class="col-xs-3"> 
                {!! Field::text('cuenta', ['ph' => 'cuenta','required']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3"> 
                {!! Field::text('destinatario', ['ph' => 'Destinatario','required']) !!}
            </div>
            <div class="col-xs-3">
                {!! Field::text('direccion', ['ph' => 'direcci&oacute;n','required']) !!}
            </div>
            <div class="col-xs-3"> 
                {!! Field::number('telefono', ['ph' => 'Tel&acute;fono','required']) !!}
            </div>
        </div>        <div class="row">
            <div class="col-xs-2"> 
                {!! Form::submit('Guardar', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#cuenta").focusout(function() {
                var url = "digitacion/cuenta/";           
                var cuenta=$('#cuenta').val();
                $.get(url+cuenta, function(data) {
                    //alert(data);
                    $('#destinatario').val(data.destinatario);
                    $('#direccion').val(data.direccion);
                    $('#telefono').val(data.telefono);
                });
            });
        });
    </script>
@endsection