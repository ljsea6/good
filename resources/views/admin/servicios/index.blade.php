@extends('templates.dash')

@section('titulo', 'Listado de servicios')

@section('content')
	<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading font-header">Listado de Servicios</div>
        <div class="panel-body">
			{!! Alert::render() !!}
	    	<a href="{{ route('admin.servicios.create') }}" class="btn btn-primary">Nuevo servicio</a>
	    	<br><br>
	        <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
		        <table data-order='[[ 0, "asc" ]]' id="tabla_servicios" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
		            <thead>
		                <tr>
		                    <th>ID</th>
		                    <th>Nombre</th>
		                    <th>Valor</th>
		                    <th>Porcentaje IVA</th>
	                    	<th>Padre</th>
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
    $('#tabla_servicios').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pagingType: "full_numbers",
        ajax: '{!! route('servicios.data') !!}',
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false },
            { data: 'nombre', name: 'nombre'},
            { data: 'valor', name: 'valor', className: "centrar" },
            { data: 'porc_iva', name: 'porc_iva', className: "centrar" },
            { data: 'padre', name: 't.nombre' ,orderable: false, searchable: false },
            { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
        ],
        
        "language": {
            "url": "{{ asset('css/Spanish.json') }}"
        }
    });
});
</script>
@endpush