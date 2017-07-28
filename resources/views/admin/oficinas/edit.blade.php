@extends('templates.dash')

@section('titulo','Editar Oficina')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
    <div class="box-header with-border">
        <h3 class="box-title">Editar Oficina</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::model($sucursal,['route' => ['admin.oficinas.update', $sucursal->id] , 'method' => 'PUT' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::text('direccion', ['ph' => 'Nombre','required']) !!}
                {!! Field::text('telefono', ['ph' => 'Nombre','required']) !!}
                {!! Field::text('email', ['ph' => 'Nombre','required']) !!}
                {!! Field::select('ciudad_id', $ciudades->toArray(), ['required']) !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection