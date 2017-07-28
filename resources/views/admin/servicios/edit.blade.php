@extends('templates.dash')

@section('titulo','Editar servicio')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Editar servicio</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::model($servicio,['route' => ['admin.servicios.update', $servicio->id] , 'method' => 'PUT' ]) !!}
                {!! Field::text('nombre', ['ph' => '', 'required']) !!}
                {!! Field::number('valor', ['ph' => '', 'required','class' => 'text-right']) !!}
                {!! Field::number('porc_iva', ['ph' => '', 'required','class' => 'text-right']) !!}
                {!! Field::select('padre_id', $servicios->toArray(), ['required']) !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection