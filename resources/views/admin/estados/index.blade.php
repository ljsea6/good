@extends('templates.dash')

@section('titulo', 'Listado de estados')

@section('content')
	<div class="box">
	    <div class="box-body">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de estados</div>
            <div class="panel-body">
                {!! Alert::render() !!}
                <a href="{{ route('admin.estados.create') }}" class="btn btn-primary">
                    Nuevo estado
                </a>
	    	<div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
	        <table data-order='[[ 0, "asc" ]]' id="tabla_estados" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
	            <thead>
	                <tr>
	                    <th>ID</th>
	                    <th>Nombre</th>
                        <th>Alias</th>
                        <th>Priodidad</th>
	                    <th>Modulo</th>
	                    <th>Padre</th>
	                    <th>Acciones</th>
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
    $('#tabla_estados').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pagingType: "full_numbers",
        ajax: '{!! route('estados.data') !!}',
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false },
            { data: 'nombre', name: 'estados.nombre'},
            { data: 'alias', name: 'estados.alias' },
            { data: 'prioridad', name: 'estados.prioridad' },
            { data: 'modulo', name: 'estados.modulo' },
            { data: 'padre', name: 't.nombre' },
            { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
        ],
        
        "language": {
            "url": "{{ asset('css/Spanish.json') }}"
        }
    });
});
</script>
@endpush