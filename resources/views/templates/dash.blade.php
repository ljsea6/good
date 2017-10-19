<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('titulo') | Home </title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" href="{{ asset('img/r_icono.png') }}" type="image/x-icon">
    {!! Html::style('css/bootstrap.min.css') !!}
    {!! Html::style('css/icon.css') !!}
    {!! Html::style('css/font-awesome.min.css') !!}
    {!! Html::style('css/animate.min.css') !!}
    {!! Html::style('css/animsition.min.css') !!}
    {!! Html::style('css/style.css') !!}
    {!! Html::style('css/select2.min.css') !!}
    {!! Html::style('css/bootstrap-datetimepicker.min.css') !!}
    <!-- Para darle formato a las tablas-->
    {!! Html::style('css/jquery.dataTables.min.css') !!}
    {!! Html::style('css/buttons.dataTables.min.css') !!}

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.0.2/css/responsive.bootstrap.min.css">

    {!! Html::style('js/Jit/Examples/css/base.css') !!}
    {!! Html::style('js/Jit/Examples/css/Spacetree.css') !!}

    {!! Html::style('css/app.min.css') !!}

     @yield('styles')
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="wrapper side-nav-sm hover-expand" style="animation-duration: 0.5s; opacity: 1;">
        @include('templates.dash.header')
        @include('templates.dash.sidebar')
        <section class="main-container">
            <div class="content-wrap">
                @yield('content')
            </div>
        </section>
        @include('templates.dash.footer')
    </div>
    {!! Html::script('js/jquery-1.11.2.min.js') !!}
    {!! Html::script('js/bootstrap.min.js') !!}
    {!! Html::script('js/modernizr.min.js') !!}
    {!! Html::script('js/jquery.slimscroll.min.js') !!}
    {!! Html::script('js/jquery.animsition.min.js') !!}
    {!! Html::script('js/jquery.sparkline.min.js') !!}
    {!! Html::script('js/jquery.flot.min.js') !!}
    {!! Html::script('js/simplecalendar.js') !!}
    {!! Html::script('js/select2.js') !!}
    {!! Html::script('js/es.js') !!}
    {!! Html::script('js/skycons.js') !!}
    {!! Html::script('js/jquery.noty.packaged.min.js') !!}
    {!! Html::script('js/jquery.cookie.js') !!}
    {!! Html::script('js/app.min.js') !!}
    {!! Html::script('js/steps.js') !!}
    {!! Html::script('js/jquery.multi-select.js') !!}
    {!! Html::script('js/moment.min.js') !!}
    {!! Html::script('js/bootstrap-datetimepicker.min.js') !!}
     <!-- DataTables -->
    {!! Html::script('js/jquery.dataTables.min.js') !!}
    {!! Html::script('js/dataTables.buttons.min.js') !!}
    {!! Html::script('js/buttons.flash.min.js') !!}
    {!! Html::script('js/jszip.min.js') !!}
    {!! Html::script('js/pdfmake.min.js') !!}
    {!! Html::script('js/vfs_fonts.js') !!}
    {!! Html::script('js/buttons.html5.min.js') !!}
    {!! Html::script('js/buttons.print.min.js') !!}


    {!! Html::script('js/dataTables.bootstrap.min.js') !!}
    
    <script src="https://cdn.datatables.net/responsive/2.0.2/js/dataTables.responsive.min.js"></script>
 
    {!! Html::script('js/Jit/Extras/excanvas.js') !!}
    {!! Html::script('js/Jit/jit.js') !!}
    {!! Html::script('js/Jit/Examples/Spacetree/example1.js') !!}
    @yield('scripts')
</body>
</html>