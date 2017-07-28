@extends('templates.dash')

@section('titulo','Crear rol')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <b>
            Crear rol
        </b>
        <ul class="panel-toolbar list-unstyled">
            <li class="dropdown collapse-option">
                <a href="#" class="text-muted dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-ellipsis-h">
                    </i>
                </a>
                <ul class="dropdown-menu dropdown-animated fade-effect closed">
                    <li>
                        <a href="#" class="text-muted refresh-widget">
                            <i class="icon-spinner9">
                            </i>
                        </a>
                    </li>
                    <li>
                        <a href="#collapsedPanel" class="expand-widget" data-toggle="collapse" aria-expanded="true">
                            <i class="icon-circle-up">
                            </i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="fullscreen-widget">
                            <i class="icon-enlarge">
                            </i>
                        </a>
                    </li>
                    <li>
                        <a>
                            <i class="icon-cross">
                            </i>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div id="collapsedPanel" class="collapse in">
        <div class="panel-body">
            <div class="col-xs-6">
                {!! Form::open(['route' => ['admin.perfiles.store'] , 'method' => 'POST' ]) !!}
                {!! Field::text('name',['label' => 'Nombre']) !!}
                {!! Field::text('description',['label' => 'Descripción']) !!}
                {!! Form::submit('Enviar!',['class' => 'btn btn-success btn-lg pull-right']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="loading-wrap">
        <div class="loading-dots">
            <div class="dot1">
            </div>
            <div class="dot2">
            </div>
        </div>
    </div>
</div>
@endsection
