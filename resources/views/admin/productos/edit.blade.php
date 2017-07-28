@extends('templates.dash')

@section('titulo','Editar producto')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <h3 class="box-title">Editar producto</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::model($producto,['route' => ['admin.productos.update', $producto->id] , 'method' => 'PUT' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::text('descripcion', ['ph' => 'Descripción','required']) !!}
                {!! Field::select('tipo_id', $tipos->toArray(), ['required']) !!}
                {!! Form::submit('Guardar', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection