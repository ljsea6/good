@extends('templates.dash')

@section('titulo','Crear Courier')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                    <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                    <p>Paso 1</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                    <p>Paso 2</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                    <p>Paso 3</p>
                </div>
            </div>
        </div>
        {!! Form::open(['route' => ['admin.couriers.store'] , 'method' => 'POST' , 'files' => true]) !!}
            <div class="row setup-content" id="step-1">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3> Datos básicos: </h3>
                        <hr>
                        <div class="avatarWrapper">
                            <div class="avatar">
                                <div class="uploadOverlay">
                                    <i class="fa fa-cloud-upload"></i>
                                </div>
                                <input type='file' id="avatar" name="avatar" />
                                <img id="target" src="{{ asset('img/avatar-bg.png') }}" alt="Avatar" />
                            </div>
                            @if ($errors->has('avatar'))
                                {!! $errors->first('avatar', '<span class="help-block" style="width: 200px; color: red;">:message</span>') !!}
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                {!! Field::select('tipo_identificacion_id', $tipoIdentificacion->toArray(), ['required']) !!}
                            </div>
                            <div class="col-md-9">
                                {!! Field::number('identificacion', ['ph' => 'Identificacion' , 'required']) !!}
                            </div>
                        </div>                        <div class="row">
                            <div class="col-md-6">
                                {!! Field::text('nombres', ['ph' => 'Nombres', 'required']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! Field::text('apellidos', ['ph' => 'Apellidos', '']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                               {!! Field::text('direccion', ['ph' => 'Direccion' , 'required']) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Field::text('telefono', ['ph' => 'Telefono' , 'required','maxlength' => 10]) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Field::text('celular', array('placeholder' => '','maxlength' => 10 )) !!}
                            </div>                     
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                {!! Field::email('email', ['ph' => 'Email' , 'required']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! Field::select('ciudad_id', $ciudades->toArray(), ['required']) !!}
                            </div>
                        </div>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Siguiente</button>
                        <br><br><br>
                        <br><br><br>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-2">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3> Creacion de usuario del courier : </h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                {!! Field::text('nombres_usuario_courier', ['ph' => 'Nombre usuario courier' , 'required']) !!}
                            </div>
                             <div class="col-md-6">
                                {!! Field::text('apellidos_usuario_courier', ['ph' => 'Apellido usuario courier' , 'required']) !!}
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-6">
                                {!! Field::text('telefono_usuario_courier', ['ph' => 'Telefono usuario courier' , 'required']) !!}
                            </div>
                             <div class="col-md-6">
                                {!! Field::email('email_usuario_courier', ['ph' => 'Email usuario courier' , 'required']) !!}
                            </div>
                        </div>
                        <div class="row"> 
                             <div class="col-md-6">
                                {!! Field::text('usuario', ['ph' => 'Usuario courier' , 'required']) !!}
                            </div>
                             <div class="col-md-6">
                                {!! Field::password('contraseña', ['ph' => '********' , 'required']) !!}
                            </div>
                        </div>
                         <button class="btn btn-primary nextBtn btn-lg pull-right" type="button">Siguiente</button>
                        <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Anterior</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-3">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3> Estados Equivalentes : </h3>
                        <hr>
                        @for( $i=0; $i<$estados->count(); $i++)
                            <div class="col-md-6">{{  $estados[$i]->nombre }}</div>
                            <div class="col-md-6">
                                <input type="text" name="estados_<?=$i?>" value="{{ $estados[$i]->nombre }}">
                            </div>
                            <input type="hidden" name="estados_original_<?=$i?>" value="{{ $estados[$i]->nombre }}">
                        @endfor

                        <input type="hidden" name="estados" value="{{ $estados->count() }}">

                        <button class="btn btn-success btn-lg pull-right" type="submit">Guardar Datos</button>
                         <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Anterior</button>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function (e) {
                $('#target').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#avatar").change(function(){
        readURL(this);
    });
    $('#ciudad_id').select2({
                language: "es",
                placeholder: "Seleccionar ciudad...",
                allowClear: true
    });
</script>
@endsection