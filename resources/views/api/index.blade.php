@extends('templates.main')

@section('titulo','Api Good')

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
            top: 50%;
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
    <div class="fullscreen-bg" style="position: initial">
        <video loop muted autoplay poster="{{ asset('video/Coverr-office.jpg') }}" class="fullscreen-bg__video">
            <source src="{{ asset('video/Coverr-office.mp4') }}" type="video/mp4"/>
            <source src="{{ asset('video/Coverr-office.webm') }}" type="video/webm"/>
        </video>
    </div>
    <div class="wrapper animsition">
        <div class="container text-center">
            <div class="single-wrap">
                {!! Form::open(['route' => 'access.login', 'method' => 'POST']) !!}
                <div class="single-inner-padding text-center">
                    <img src="{{ asset('img/Hello.jpg') }}" class="img-responsive text-center" style="display: inline-block">
                    <input type="hidden" id="grant_type" name="grant_type" value="password">
                    <input type="hidden" id="client_id" name="client_id" value="65n6yfbn45654m67yrt">
                    <input type="hidden" id="client_secret" name="client_secret" value="34b5454 54567 7btjb867biib5n85">
                    <div class="form-group form-input-group m-t-30 m-b-5">
                        {!! Field::text('username', ['ph' => 'Usuario', 'class' => 'input-lg font-14']) !!}
                        {!! Field::password('password', ['ph' => '********', 'class' => 'input-lg font-14']) !!}
                    </div>
                    <div class="m-l-10 font-11 text-left">
                        <div class="checkbox">
                            <div class="custom-checkbox">
                                <input type="checkbox" name="remember" id="remember" />
                                <label for="remember">Recordarme</label>
                            </div>
                        </div>
                    </div>
                    {!! Form::submit('Iniciar sesión', ['class' => 'btn btn-main btn-lg btn-block font-14 m-t-30']) !!}
                    <div class="m-t-15 text-right">
                    <!-- <a href="{{ route('reset') }}">Olvido contraseña?</a> -->

                    </div>
                    <div class="m-t-15 text-right">
                    <!-- <a href="{{ route('Registro') }}">Registrarse</a> -->
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection
