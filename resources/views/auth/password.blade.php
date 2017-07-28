@extends('templates.main')

@section('titulo','Login Domina')

@section('styles')
<style type="text/css">
.container{
    margin-top: 5%;
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
            <div class="single-inner-padding text-center">
                <img src="{{ asset('img/logo_color.png') }}" class="img-responsive"/>
                <p class="login-box-msg">Recuperar contraseña</p>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                {!! Form::open(['route' => 'reset', 'method' => 'POST']) !!}
                    {!! Field::email('email',['ph' => 'Correo electronico']) !!}
                    {!! Form::submit('Recuperar!',['class' => 'btn btn-primary btn-block btn-flat']) !!}
                    <br><br>
                    <div class="text-right">
                        <a href="{{ route('login') }}">Iniciar sesion</a><br>
                    </div>
                {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection