<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('titulo')</title>
   
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="{{ asset('img/r_icono.png') }}" type="image/x-icon">
    {!! Html::style('css/bootstrap.min.css') !!}
    {!! Html::style('css/icon.css') !!}
    {!! Html::style('css/font-awesome.min.css') !!}
    {!! Html::style('css/animate.min.css') !!}
    {!! Html::style('css/app.min.css') !!}
    {!! Html::style('js/Jit/Examples/css/base.css') !!}
    {!! Html::style('js/Jit/Examples/css/Spacetree.css') !!}
    @yield('styles')
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    @yield('content')
    {!! Html::script('js/jquery-1.11.2.min.js') !!}
    {!! Html::script('js/bootstrap.min.js') !!}
    {!! Html::script('js/modernizr.min.js') !!}
    {!! Html::script('js/jquery.slimscroll.min.js') !!}
    {!! Html::script('js/jquery.animsition.min.js') !!}
    {!! Html::script('js/Jit/Examples/Extras/excanvas.js') !!}
    {!! Html::script('js/Jit/jit.js') !!}
    {!! Html::script('js/Jit/Examples/Spacetree/example1.js') !!}
    @yield('scripts')
</body>
</html>