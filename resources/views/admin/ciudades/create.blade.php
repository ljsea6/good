@extends('templates.dash')

@section('titulo','Crear ciudades')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
    <div class="box-header with-border">
        <h3 class="box-title">Crear ciudades</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::open(['route' => ['admin.ciudades.store'] , 'method' => 'POST' ]) !!}
                {!! Field::number('codigo_dane', ['required']) !!}
                {!! Field::text('nombre' , ['required']) !!}
                {!! Field::text('departamento', ['required']) !!}
                {!! Field::text('region' , ['required']) !!}
                {!! Field::select('tipo_id', $tipos_ciudades->toArray(), ['required']) !!}
                {!! Field::text('Similares') !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>
</div>
@endsection