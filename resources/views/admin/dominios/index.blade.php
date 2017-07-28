@extends('templates.dash')

@section('titulo', 'Listado de dominios')

@section('content')
	<div class="box">
	    <div class="box-body">
        	<div class="panel panel-default">
            <div class="panel-heading font-header">Listado de dominios</div>
            <div class="panel-body">
			{!! Alert::render() !!}
	    	<a href="{{ route('admin.dominios.create') }}" class="btn btn-primary">Nuevo Dominio</a>
	    	<br><br>
	        <table id="tabla_dominios" class="table-hover table-striped table-bordered">
	            <thead>
	                <tr>
	                    <th>Id</th>
	                    <th>Nombre</th>
	                    <th>Padre</th>
	                    <th style="text-align: center;">Accciones</th>
	                </tr>
	            </thead>
	        </table>
	       </div>
	       </div>
	</div>
@stop

@push('scripts')
<script>
$(function() {
    $('#tabla_dominios').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pagingType: "full_numbers",
        ajax: '{!! route('dominios.data') !!}',
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false},
            { data: 'nombre', name: 'tipos.nombre'},
            { data: 'padre_id', name: 'tipos.padre_id' },
            { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
        ],
        
        "language": {
            "url": "{{ asset('css/Spanish.json') }}"
        }
    });
});
</script>
@endpush