@extends('templates.dash')

@section('titulo','Crear Dominio')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <div class="box-header with-border">
        <h3 class="box-title">Crear dominio</h3>
        </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::open(['route' => ['admin.dominios.store'] , 'method' => 'POST' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::select('padre_id', $dominios->toArray(), ['required']) !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection