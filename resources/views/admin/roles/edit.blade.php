@extends('templates.dash')

@section('titulo','Editar Rol')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Editar Rol</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-6">
            {!! Form::model($rol,['route' => ['admin.roles.update', $rol->id] , 'method' => 'PUT' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection