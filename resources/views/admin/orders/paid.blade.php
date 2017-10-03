@extends('templates.dash')

@section('titulo', 'Ordenes Pagas')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Ordenes Pagas</div>
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
                            <th>Título</th>
                            <th>#Orden</th>
                            <th>Precio Unidad</th>
                            <th>Cantidad</th>
                            <th>Costo Envio</th>
                            <th>Fecha Compra</th>
                            <th>Total</th>
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
            var table = $('#orders').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                processing: true,
                serverSide: false,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('admin.orders.list.paid')}}',
                columns: [
                    { data: 'nombre_producto', name: 'nombre_producto', orderable: true, searchable: true },
                    { data: 'numero_orden', name: 'numero_orden', orderable: true, searchable: true },
                    { data: 'precio_unidad', name: 'precio_unidad', orderable: true, searchable: true },
                    { data: 'cantidad', name: 'cantidad', orderable: true, searchable: true },
                    { data: 'costo_envio', name: 'costo_envio', orderable: true, searchable: true },
                    { data: 'fecha_compra_cliente', name: 'fecha_compra_cliente', orderable: true, searchable: true},
                    { data: 'total', name: 'total', orderable: false, searchable: true },

                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },

            });

            table
                .column( '0:visible' )
                .order( 'desc' )
                .draw();

        });
    </script>
@endpush