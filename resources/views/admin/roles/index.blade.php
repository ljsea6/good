@extends('templates.dash')

@section('titulo', 'Listado de roles')

@section('content')
	<div class="box">
	    <div class="box-header">
	        <div class="box-title">Listado de roles</div>
	    </div>
	    <div class="box-body">
			{!! Alert::render() !!}
	    	<a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Nuevo Rol</a>
	    	<br><br>
	        <table class="table table-bordered">
	            <thead>
	                <tr>
	                    <th>ID</th>
	                    <th>Nombre</th>
	                    <th>Acciones</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach ($roles as $rol)
	                	<tr>
	                		<td>
	                			{{ $rol->id }}
	                		</td>
	                		<td>
	                    			{{ $rol->nombre }}
	    
	                    	<td>
	                    		<a href="{{ route('admin.roles.edit', $rol->id) }}" class="btn btn-warning">
                                    <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                                </a>
                                <a href="{{ route('admin.roles.destroy', $rol->id) }}" onclick="return confirm('¿Seguro que deseas eliminarlo?')" class="btn btn-danger">
                                    <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                                </a>
	                    	</td>
	                	</tr>
	                @endforeach
	            </tbody>
	        </table>
	        {!! $roles->render() !!}
	    </div>
	</div>
@stop