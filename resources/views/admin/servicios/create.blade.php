@extends('templates.dash')

@section('titulo','Crear servicios')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
    <div class="box-header with-border">
        <h3 class="box-title">Crear servicios</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::open(['route' => ['admin.servicios.store'] , 'method' => 'POST' ]) !!}
                {!! Field::text('nombre', ['ph' => '', 'required']) !!}
                {!! Field::number('valor', ['ph' => '', 'required','class' => 'text-right']) !!}
                {!! Field::number('porc_iva', ['ph' => '', 'required','class' => 'text-right']) !!}
                {!! Field::select('padre_id', $servicios->toArray(), ['required']) !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection