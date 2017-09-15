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
                @if (session('error'))
                    <div class="panel-footer">
                        <div class="alert alert-danger alert-dismissable">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>{{ session()->get('error') }}</strong>
                        </div>
                    </div>
                @endif
                    <form method="post" action="{{route('oauth.authorize.post', $params)}}" class="form form-horizontal">
                        <div class="single-inner-padding text-center">
                            <div class="alert alert-success" role="alert">
                                    <p>La aplicación <strong><span class="label label-danger">{{$client->getName()}}</span></strong> solicta permisos de usuario para continuar.</p>
                            </div>

                            <img src="{{ asset('img/Hello.jpg') }}" class="img-responsive text-center" style="display: inline-block">
                            {{ csrf_field() }}

                            <input type="hidden" name="client_id" value="{{$params['client_id']}}">

                            <input type="hidden" name="redirect_uri" value="{{$params['redirect_uri']}}">

                            <input type="hidden" name="response_type" value="{{$params['response_type']}}">


                            <input type="hidden" name="state" value="{{$params['state']}}">


                            <input type="hidden" name="scope" value="{{$params['scope']}}">
                            <button type="submit" class="btn btn-primary" name="approve" value="1">Aprobar</button>
                            <button type="submit" class="btn btn-primary" name="deny" value="1">Denegar</button>
                        </div>
                    </form>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection