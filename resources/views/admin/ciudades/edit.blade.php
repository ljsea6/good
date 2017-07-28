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
            {!! Form::model($ciudad,['route' => ['admin.ciudades.update' , $ciudad->id] , 'method' => 'PUT' ]) !!}
                {!! Field::number('codigo_dane') !!}
                {!! Field::text('nombre') !!}
                {!! Field::text('departamento') !!}
                {!! Field::text('region') !!}
                {!! Field::select('tipo_id', $tipos_ciudades->toArray(), ['required']) !!}
                {!! Field::text('similares') !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection