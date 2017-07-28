@extends('templates.dash')

@section('titulo','Crear producto')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <h3 class="box-title">Crear producto</h3>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                {!! Form::open(['route' => ['admin.productos.store'] , 'method' => 'POST' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::text('descripcion', ['ph' => 'Descripción','required']) !!}
                {!! Field::select('tipo_id', $tipos->toArray(), ['required', '']) !!}
                {!! Form::submit('Guardar', ['class' => 'btn btn-success btn-lg pull-right']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
