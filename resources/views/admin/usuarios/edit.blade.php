@extends('templates.dash')

@section('titulo','Editar usuario')

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
            </div>
        </div>
        {!! Form::model($usuario, ['route' => ['admin.users.update', $usuario->id] , 'method' => 'PUT' , 'files' => true]) !!}
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
                                @if ($usuario->avatar !== null)
                                    <img id="target" src="{{ asset($usuario->avatar) }}" alt="Avatar" />
                                @else
                                    <img id="target" src="{{ asset('img/avatar-bg.png') }}" alt="Avatar" />
                                @endif
                                
                            </div>
                            @if ($errors->has('avatar'))
                                {!! $errors->first('avatar', '<span class="help-block" style="width: 200px; color: red;">:message</span>') !!}
                            @endif
                        </div>
                         <div class="row">
                        <div class="col-md-6">
                        {!! Field::number('identificacion', ['ph' => 'Identificacion' , 'required']) !!}
                         </div>
                         <div class="col-md-6">
                         {!! Field::select('tipo_id', $tipos->toarray() , ['required']) !!}
                          </div>
                          </div>
                        <div class="row">
                            <div class="col-md-6">
                                {!! Field::text('nombres', ['ph' => 'Nombres', 'required']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! Field::text('apellidos', ['ph' => 'Apellidos', 'required']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                {!! Field::text('direccion', ['ph' => 'Direccion' , 'required']) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Field::text('telefono', ['ph' => 'Telefono' , 'required','maxlength' => 10]) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Field::text('celular', array('placeholder' => '','maxlength' => 10 )) !!}
                            </div>  
                            <div class="col-md-4">
                                {!! Field::email('email', ['ph' => 'Email' , 'required']) !!}
                            </div>
                        </div>
                        {!! Field::select('ciudad_id', $ciudades->toArray(), ['required']) !!}
                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Siguiente</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-2">
                <div class="col-xs-12">
                    <div class="col-md-12">
                       <h3> Creacion de usuario : </h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                {!! Field::text('usuario', ['ph' => 'Usuario' , 'required']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                {!! Field::select('oficina_id', $oficinas->toArray(), ['required']) !!}
                            </div>
                             <div class="col-md-6">
                                {!! Field::select('rol_id', $roles->toArray(), ['required']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <b>Control de acceso por IP :</b>
                                <select name='control_ip' OnChange="if (this.value==1) { $('#ips').show(); } else  { $('#ips').hide(); }" class="form-control">
                                    <option value="0">NO</option>
                                    <option value="1" <? if ($usuario->control_ip) echo 'selected'; ?>>SI</option>
                                </select>                            
                            </div>
                            <div class="col-md-6" id='ips' <? if (!$usuario->control_ip) echo "style='display:none;'"; ?>>
                                {!! Field::text('ips_autorizadas', ['ph' => 'IPs Autorizadas, separadas por coma (,)']) !!}
                            </div>
                        </div>
                        <br>
                        
                        <button class="btn btn-success btn-lg pull-right" type="submit">Guardar Datos !</button>
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


     $('#tipo_id').select3({
                language: "es",
                placeholder: "Seleccionar Tipoid...",
                allowClear: true
    });

     $('#oficina_id').select4({
                language: "es",
                placeholder: "Seleccionar Oficina...",
                allowClear: true
    });
</script>
@endsection