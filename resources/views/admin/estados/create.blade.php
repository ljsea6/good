@extends('templates.dash')

@section('titulo','Crear estado')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <h3 class="box-title">Crear estado</h3>
        <div class="box-body">
        <div class="col-xs-6">
            {!! Form::open(['route' => ['admin.estados.store'] , 'method' => 'POST' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::select('modulo', ['envios'=>'Envios','ordenes'=>'Ordenes'], ['required']) !!}
                {!! Field::text('alias', ['ph' => 'Alias/Abreviatura','required']) !!}
                {!! Field::number('prioridad',0, ['ph' => 'Prioridad']) !!}
                {!! Field::select('padre_id', $estados->toArray(), ['required']) !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection