@extends('templates.dash')

@section('titulo', 'Listado de Facturas')

@section('content')

<div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de Facturas</div>
            <div class="panel-body">
        {!! Alert::render() !!}
        <a href="{{ route('admin.facturacion.create') }}" class="btn btn-primary">
            Nueva Factura
        </a>
        <br/>
        <br/>
        <table id="tabla_ordenes" class="table-hover table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Numero</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
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
    $('#tabla_ordenes').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        pagingType: "full_numbers",
        ajax: '{!! route('facturas.data') !!}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'numero', name: 'numero'},
            { data: 'fecha', name: 'fecha' },
            {
                data: null, label: 'cliente', name: 'cliente.nombres',
                render: function ( data, type, row ) {
                    // Combine the first and last names into a single table field
                    return data.cliente.nombres+' '+data.cliente.apellidos;
                },
                editField: ['nombres', 'apellidos']
            },
            { data: 'tipo', name: 'tipo' },
        ],
        "language": {
            "url": "{{ asset('css/Spanish.json') }}"
        }
    });
});
</script>
@endpush
