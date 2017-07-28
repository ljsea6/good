@extends('templates.dash')

@section('titulo', 'Listado de productos')

@section('content')
    <div class="box">
    <div class="panel panel-default">
        <div class="panel-heading font-header">Listado de logs</div>
        <div class="panel-body">
            <br><br>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Original</th>
                        <th>Nuevo</th>
                        <th>Sentencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>
                                {{ $log->id }}
                            </td>
                            <td>
                                {{ $log->tabla }}
                            </td>
                            <td>
                                {{ substr($log->original_data,0,30) }}....
                            </td>
                            <td>
                                {{ substr($log->nuevo_data,0,30) }}....
                            </td>
                            <td>
                                {{ substr($log->query,0,30) }}....
                            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {!! $logs->render() !!}
        </div>
        </div>
    </div>
@stop