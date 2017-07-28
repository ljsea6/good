@extends('templates.dash')

@section('titulo', 'Listado de resoluciones')

@section('content')
	<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading font-header">Listado de resoluciones</div>
        <div class="panel-body">
			{!! Alert::render() !!}
	    	<a href="{{ route('admin.resoluciones.create') }}" class="btn btn-primary">Nueva resolucion</a>
	    	<br><br>
 			<div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
		        <table data-order='[[ 0, "asc" ]]' id="tabla_resoluciones" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
	            <thead>
	                <tr>
	                    <th>ID</th>
	                    <th>Nombre</th>
	                    <th>Prefijo</th>
	                    <th>Numero</th>
	                    <th>Desde</th>
	                    <th>Hasta</th>
	                    <th style="text-align: center;">Acciones</th>
	                </tr>
	            </thead>
	        	</table>
	        </div>
	    </div>
	    </div>
	</div>
@stop

@push('scripts')
<script>
$(function() {
    $('#tabla_resoluciones').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pagingType: "full_numbers",
        ajax: '{!! route('resoluciones.data') !!}',
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false },
            { data: 'nombre', name: 'nombre'},
            { data: 'prefijo', name: 'prefijo', className: "centrar" },
            { data: 'numero', name: 'numero', className: "centrar" },
            { data: 'desde', name: 'desde', className: "centrar" },
            { data: 'hasta', name: 'hasta', className: "centrar" },
            { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
        ],
        
        "language": {
            "url": "{{ asset('css/Spanish.json') }}"
        }
    });
});
</script>
@endpush