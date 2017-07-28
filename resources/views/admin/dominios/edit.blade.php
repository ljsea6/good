@extends('templates.dash')

@section('titulo','Editar dominio')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
    <div class="box-header with-border">
        <h3 class="box-title">Editar dominio</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::model($dominio,['route' => ['admin.dominios.update', $dominio->id] , 'method' => 'PUT' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::select('padre_id', $dominios->toArray()) !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
    </div>
</div>
@endsection