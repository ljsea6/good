@extends('templates.dash')

@section('titulo','Crear Oficina')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
    <div class="box-header with-border">
        <h3 class="box-title">Crear Oficina</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6 forma">
            {!! Form::open(['route' => ['admin.oficinas.store'] , 'method' => 'POST' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::text('direccion', ['ph' => 'Direccion','required']) !!}
                {!! Field::text('telefono', ['ph' => 'Telefono','required']) !!}
                {!! Field::text('email', ['ph' => 'Email','required']) !!}
                {!! Field::select('ciudad_id', $ciudades->toArray(), ['required']) !!}
                {!! Form::submit('Guardar', ['class' => 'btn btn-success btn-lg pull-right guardar', 'id' => 'guardar']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection