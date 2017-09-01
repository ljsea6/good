@extends('templates.dash')

@section('titulo', 'Gestion de Ordenes')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de Ordenes</div>
            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-info fade in col-sm-12 col-md-12 col-lg-12">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <ul>
                            <li>{{ session('status') }}</li>
                        </ul>
                    </div>
                @endif

                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">

                    <table data-order='[[ 0, "asc" ]]' id="orders" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th># Orden</th>
                            <th>Cliente</th>
                            <th>Precio</th>
                            <th>Detalle Orden</th>
                            <th>Estado</th>
                            <th>Fecha Compra</th>
                            <th>Código Envio Internacional</th>
                            <th>Código Envio Nacional</th>
                            <th>Estado Orden</th>
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

        $(document).ready(function(){
            $('#orders').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                //deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('admin.orders.paid')}}',
                columns: [
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'customer', name: 'customer', orderable: true, searchable: true },
                    { data: 'value', name: 'value', orderable: true, searchable: true  },
                    { data: 'order', name: 'order', orderable: true, searchable: true  },
                    { data: 'financial_status', name: 'financial_status', orderable: true, searchable: true },
                    { data: 'fecha_compra', name: 'fecha_compra', orderable: true, searchable: true },
                    { data: 'codigo_envio_internacional', name: 'codigo_envio_internacional', orderable: true, searchable: true },
                    { data: 'codigo_envio', name: 'codigo_envio', orderable: true, searchable: true },
                    { data: 'estado_orden', name: 'estado_orden', orderable: true, searchable: true }
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },

            });

        });



    </script>
@endpush