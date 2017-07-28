@extends('templates.dash')

@section('titulo', 'Listado de reglas')

@section('content')
    <div class="box">
    <div class="panel panel-default">
        <div class="panel-heading font-header">Listado de reglas</div>
        <div class="panel-body">
            {!! Alert::render() !!}
            <a href="{{ route('admin.reglas.create') }}" class="btn btn-primary">Nueva regla</a>
            <br><br>
            <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                <table data-order='[[ 0, "asc" ]]' id="tabla_reglas" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                <thead>
                    <tr>
                       <th>id</th>
                       <th>id_red</th>
                       <th>nivel</th>
                        <th>estado</th>
                        <th>plataforma</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
$(function() { 
  
$('#tabla_reglas').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pagingType: "full_numbers",
            ajax: '{!! route('reglas.data') !!}',
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false },
                { data: 'id_red', name: 'id_red', orderable: false, searchable: false },
                { data: 'nivel', name: 'Nombre', orderable: false },
                { data: 'estado', name: 'cantidad', orderable: false, searchable: false },
                { data: 'plataforma', name: 'profundidad', orderable: false },
                { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
            ],
             
        






            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });
    });

</script>
@endsection