@extends('templates.dash')

@section('titulo', 'Rol y permisos')

@section('content')
	<div class="box">
	    <div class="box-header">
	        <div class="box-title">Rol y permisos</div>
	    </div>
	    <div class="box-body">
			{!! Alert::render() !!}
	    	<a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Nuevo Permiso</a>
	    	<br><br>

	        <table class="table table-bordered">
	            <thead>
	                <tr>
	                    <th>Id</th>
	                    <th>Nombre</th>
	                    <th>Ruta</th>
	                    <th>Acciones</th>
	                </tr>
	            </thead>
	            <tbody>
                {!! 
                	$keys = array_keys(array_column($rutas, 'ruta'), 1);
					$new_array = array_map(function($k) use ($rutas){return $rutas[$k];}, $keys);
					print_r($new_array);
                !!}
	            </tbody>
	        </table>
	    </div>
	</div>
@stop