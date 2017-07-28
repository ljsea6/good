@extends('templates.main')

@section('titulo','Resetear contraseña - Domina')

@section('body_class','hold-transition login-page')

@section('content')
<div class="login-box">
            <div class="login-logo">
                <b>GOD</b>
            </div>
            <div class="login-box-body">
                <p class="login-box-msg">Resetear contraseña</p>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                {!! Form::open(['route' => 'reset', 'method' => 'POST']) !!}
                    <input type="hidden" name="token" value="{{ $token }}">
                    {!! Field::email('email',['ph' => 'Correo electronico']) !!}
                    {!! Field::password('password',['ph' => '*********']) !!}
                    {!! Field::password('password_confirmation',['ph' => '*********']) !!}
                    {!! Form::submit('Recuperar!',['class' => 'btn btn-primary btn-block btn-flat']) !!}
                {!! Form::close() !!}
            </div>
        </div>
@endsection