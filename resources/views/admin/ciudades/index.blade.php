@extends('templates.dash')

@section('titulo', 'Listado de ciudades')

@section('content')
	<div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de ciudades</div>
            <div class="panel-body">
			{!! Alert::render() !!}
	    	<a href="{{ route('admin.ciudades.create') }}" class="btn btn-primary">Nueva ciudad</a>
	    	<br><br>
	        <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
	        <table data-order='[[ 0, "asc" ]]' id="tabla_ciudades" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
	            <thead>
	                <tr>
	                    <th>ID</th>
	                    <th>Código dane</th>
	                    <th>Ciudad</th>
	                    <th>Departamento</th>
	                    <th>Región</th>
	                    <th>Accciones</th>
	                </tr>
	            </thead>
	        </table>
	    </div>
	</div>
@stop

@push('scripts')
<script>
$(function() {
    $('#tabla_ciudades').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pagingType: "full_numbers",
        ajax: '{!! route('ciudades.data') !!}',
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false },
            { data: 'codigo_dane', name: 'codigo_dane'},
            { data: 'nombre', name: 'nombre' },
            { data: 'departamento', name: 'departamento' },
            { data: 'region', name: 'region' },
            { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
        ],
        
        "language": {
            "url": "{{ asset('css/Spanish.json') }}"
        }
    });
});
</script>
@endpush