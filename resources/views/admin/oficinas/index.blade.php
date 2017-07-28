@extends('templates.dash')

@section('titulo', 'Listado de oficinas')

@section('content')
	<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading font-header">Listado de oficinas</div>
        <div class="panel-body">
			{!! Alert::render() !!}
	    	<a href="{{ route('admin.oficinas.create') }}" class="btn btn-primary">Nueva oficinas</a>
	    	<br><br>
			<div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
		        <table data-order='[[ 0, "asc" ]]' id="tabla_sucursales" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
	            <thead>
	                <tr>
	                    <th>ID</th>
	                    <th>Nombre</th>
	                    <th>Ciudad</th>
	                    <th>Dirección</th>
	                    <th>Telefono</th>
	                    <th style="text-align: center;">Acciones</th>
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
    $('#tabla_sucursales').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pagingType: "full_numbers",
        ajax: '{!! route('oficinas.data') !!}',
        columns: [
            { data: 'id', name: 'oficinas.id', orderable: false, searchable: false },
            { data: 'nombre', name: 'oficinas.nombre'},
            { data: 'ciudad', name: 'c.nombre', className: "centrar" },
            { data: 'direccion', name: 'oficinas.direccion', className: "centrar" },
            { data: 'telefono', name: 'oficinas.telefono', className: "centrar" },
            { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
        ],
        
        "language": {
            "url": "{{ asset('css/Spanish.json') }}"
        }
    });
});
</script>
@endpush