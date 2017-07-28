@extends('templates.main')

@section('titulo','Login God')

@section('styles')
<style type="text/css">
.container{
    margin-top: -4%;
}
.single-wrap{
    border-radius: inherit;
}
.single-wrap:before{
    display: none;
}
#field_usuario .control-label, #field_password .control-label{
    display: none;
}
.fullscreen-bg {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    overflow: hidden;
    z-index: -100;
}
.fullscreen-bg__video {
    position: absolute;
    top: 60%;
    left: 50%;
    width: auto;
    height: auto;
    min-width: 100%;
    min-height: 100%;
    -webkit-transform: translate(-20%, -10%);
        -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
}
@media (max-width: 767px) {
    .fullscreen-bg {
        background: url("{{ asset('video/Coverr-office.jpg') }}") center center / cover no-repeat;
    }
    .fullscreen-bg__video {
        display: none;
    }
}
</style>
@endsection

@section('content')
<div class="fullscreen-bg">
    <video loop muted autoplay poster="{{ asset('video/Coverr-office.jpg') }}" class="fullscreen-bg__video">
        <source src="{{ asset('video/Coverr-office.mp4') }}" type="video/mp4"/>
        <source src="{{ asset('video/Coverr-office.webm') }}" type="video/webm"/>
    </video>
</div>
<div class="wrapper animsition">
    <div class="container text-center">
        <div class="single-wrap">
            
           {!! Form::open(['route' => ['admin.usuarios.storenuevo'] , 'method' => 'POST' , 'files' => true]) !!}
            <div class="single-inner-padding text-center">
                <img src="{{ asset('img/logo_color.png') }}" class="img-responsive"/>
                <div class="form-group form-input-group m-t-2 m-b-5">
                    {!! Field::text('nombres', ['ph' => 'Usuario', 'class' => 'input-lg font-14','required']) !!}
                    {!! Field::email('email', ['ph' => 'Email', 'class' => 'input-lg font-14','required']) !!}
                    {!! Field::password('contraseña', ['ph' => '********', 'class' => 'input-lg font-14','required']) !!}
                    {!! Field::select('tipo_id', $tipos->toarray() , ['required']) !!}
                   {!! Field::email('email_Patrocinador', ['ph' => 'Email', 'class' => 'input-lg font-14']) !!}

                </div>
                <div class="m-l-10 font-11 text-left">
                    
                </div>
                <div id='ver' style='display:block;'>
                {!! Form::submit('Guardar', ['class'=>'btn btn-success btn-lg btn-block  Guardar font-14 m-t-1' ])!!} 
                </div>
               
                <div id='ocultar' style='display:none;'>
                 {!! Form::submit('Pagar con Payu', ['class'=>'btn btn-success btn-lg btn-block  Guardar font-14 m-t-1' ])!!} 
                </div>
               <!--  <button class="btn btn-success btn-lg btn-block guardar" style="font-14 m-t-1" type="submit">Guardar</button> -->
               
            </div>
            
            {!! Form::close() !!}
        </div>
    </div>
</div>



@endsection

@section('scripts')
@endsection