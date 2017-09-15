@extends('templates.main')

@section('titulo','Api Good')

@section('styles')
    <style>
        .logo-alert{
            position: relative;
            display: block;
            max-width: 300px;
            margin: 0 auto;
        }

        .alert{
            color: white;
            text-align: justify;
        }

        .boton{
            background-color: white;
            color: gray;
            transition: .3s ease-out
        }

        .boton:hover{
            transition: .3s ease-in;
            background: #363636;
            color: white;
        }

    </style>
@endsection
@section('content')

    <div style="background: url(http://res.cloudinary.com/www-virgin-com/virgin-com-prod/sites/virgin.com/files/Articles/Entrepreneur%20Getty/Entrepreneur_breakfast_getty_2.jpg) no-repeat center / cover; height: 100vh">
        <div style="display: flex; justify-content: center; align-items: center; max-width: 400px; height: 400px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; box-shadow: 0 0 12px 3px rgba(0,0,0,.3); background: url(https://cdn.shopify.com/s/files/1/1935/1047/files/formularios_aae53d1f-29e4-40e6-b6ae-97e3db315af6.png?4265544440743003433) no-repeat center / cover; box-sizing: border-box">
            <div class="row">
                <img src="https://cdn.shopify.com/s/files/1/1935/1047/files/logo_65771d4d-e60d-4306-927a-e126a6ced852.png?18373408933088602249" alt="" class="logo-alert img-fluid">
                <div class="col-xs-12 col-md-8 col-md-offset-2">
                    <p class="alert">La aplicación {{$client->getName()}} solicita permisos de usuario para continuar.</p>
                </div>
                <div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
                    <form method="post" action="{{route('oauth.authorize.post', $params)}}" class="form form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" name="client_id" value="{{$params['client_id']}}">
                        <input type="hidden" name="redirect_uri" value="{{$params['redirect_uri']}}">
                        <input type="hidden" name="response_type" value="{{$params['response_type']}}">

                        <input type="hidden" name="state" value="{{$params['state']}}">

                        <input type="hidden" name="scope" value="{{$params['scope']}}">
                        <button type="submit" class="btn boton" name="approve" value="1">Aprobar</button>
                        <button type="submit" class="btn boton" name="deny" value="1">Denegar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection