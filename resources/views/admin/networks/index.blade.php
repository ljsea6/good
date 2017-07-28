@extends('templates.dash')

@section('titulo', 'Listado de oficinas')

@section('content')
    <div class="box">
    <div class="panel panel-default">
        <div class="panel-heading font-header">Listado de Redes</div>
        <div class="panel-body">
            @if (session('status'))
                <div class="alert alert-success fade in">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ session('status') }}
                </div>
            @endif
            <a href="{{route('admin.networks.create')}}" class="btn btn-primary">Nueva red</a>
            <br><br>
            <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                <table data-order='[[ 0, "asc" ]]' id="networks" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                    <thead>
                        <tr>
                           <th>#</th>
                            <th>Nombre</th>
                            <th>Cantidad Frontal</th>
                            <th>Profundidad</th>
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
  
$('#networks').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            processing: true,
            serverSide: true,
            deferRender: true,
            pagingType: "full_numbers",
            ajax: '{{route('admin.networks.data')}}',
            columns: [
                { data: 'id', name: 'id', orderable: true, searchable: false },
                { data: 'name', name: 'name', orderable: true, searchable: true  },
                { data: 'frontal_amount', name: 'frontal_amount', orderable: true, searchable: false },
                { data: 'depth', name: 'depth', orderable: true },
                { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
            ],
             
            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });
    });
</script>
@endpush