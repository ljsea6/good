@extends('templates.dash')

@section('titulo', 'Listado de Couries')

@section('content')

<div class="box">
    <div class="box-body">
        <div class="panel panel-default">
        <div class="panel-heading font-header">Listado de Courriers</div>
        <div class="panel-body">
        {!! Alert::render() !!}
        <a href="{{ route('admin.couriers.create') }}" class="btn btn-primary">
            Nuevo Courier
        </a>
        <br/>
        <br/>
        <table id="tabla_couriers" class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        Avatar
                    </th>
                    <th>
                        Identificación
                    </th>
                    <th>
                        Nombres
                    </th>
                    <th>
                        Apellidos
                    </th>
                    <th>
                        Dirección
                    </th>
                    <th>
                        Ciudad
                    </th>
                    <th>
                        Email
                    </th>
                    <th>
                        Acciones
                    </th>
                </tr>
            </thead>
            </tbody>
        </table>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
$(function() {
    $('#tabla_couriers').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pagingType: "full_numbers",
        ajax: '{!! route('couriers.data') !!}',
        columns: [
            { data: 'avatar', name: 'avatar' },
            { data: 'identificacion', name: 'identificacion'},
            { data: 'nombres', name: 'nombres' },
            { data: 'apellidos', name: 'apellidos' },
            { data: 'direccion', name: 'direccion' },
            { data: 'ciudad.nombre', name: 'ciudad' },
            { data: 'email', name: 'email' },
            { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
        ],
        
        "language": {
            "url": "{{ asset('css/Spanish.json') }}"
        }
    });
});
</script>
@endpush

