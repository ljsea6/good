@extends('templates.main')

@section('titulo','Login God')

@section('styles')
<style type="text/css">
.container{
    margin-top: -5%;
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
    -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
}
@media (max-width: 767px) {
    .fullscreen-bg {
        background: url(" {{ asset('video/Coverr-office.jpg') }} ") center center / cover no-repeat;
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
                <div class="form-group form-input-group m-t-30 m-b-5">
                    {!! Field::text('Nombre', ['ph' => 'Usuario', 'class' => 'input-lg font-14']) !!}
                     {!! Field::Email('email', ['ph' => 'email', 'class' => 'input-lg font-14']) !!}
                    {!! Field::password('Contraseña', ['ph' => '********', 'class' => 'input-lg font-14']) !!}
                    {!! Field::select('tipo_id', $tipos->toarray() , ['required']) !!}
                    {!! Field::Email('email_patrocinador', ['ph' => 'email', 'class' => 'input-lg font-14']) !!}
                </div>
                
                {!! Form::submit('pagar con pay u', ['class' => 'btn btn-main btn-lg btn-block font-14 m-t-30']) !!}
                
                
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection