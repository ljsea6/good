@extends('templates.dash')

@section('titulo','Buscar Referido')

@section('content')

    @if(currentUser()->tipo_id == 2)
        <div class="panel panel-default">
            <div class="panel panel-heading">
                Resultados de la busqueda
            </div>
            <div class="panel-body">
                @if(count($tercero) > 0)
                    <div class="alert alert-success alert-dismissable col-md-6 col-md-offset-3">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>{{$tercero[0]['email']}}</strong> está registrado en nuestra base de datos.
                        <br>
                        <p class="text-left"><strong>Nombre: </strong> {{$tercero[0]['nombres']}}</p>
                        <p class="text-left"><strong>Correo: </strong> {{$tercero[0]['email']}}</p>
                        <p class="text-left"><strong>Código Referido: </strong> {{$tercero[0]['apellidos']}}</p>
                    </div>

                    <div class="col-md-12">
                        <a class="btn btn-default" href="{{route('admin.index')}}" role="button">Atras</a>
                    </div>
                    @endif
                @if(count($tercero) == 0)
                        <div class="alert alert-danger alert-dismissable col-md-6 col-md-offset-3">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>No se ha encontrado ningún registo.</strong>
                        </div>
                        <div class="col-md-12">
                            <a class="btn btn-default" href="{{route('admin.index')}}" role="button">Atras</a>
                        </div>
                    @endif
            </div>
        </div>
    @endif


@endsection

