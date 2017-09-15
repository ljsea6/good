@extends('templates.dash')

@section('titulo', 'Reporte Referidos')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado Reporte</div>
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
                            <th>#</th>
                            <th># Orden</th>
                            <th># Checkout</th>
                            <th>Valor</th>
                            <th>Estado Shopify</th>
                            <th>Estado Mercado Pago</th>
                            <th>Método de Pago</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado Ordenes Por Estados</div>
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

                    <table data-order='[[ 0, "asc" ]]' id="status" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th># Cantidad</th>
                            <th>Estado Shopify</th>
                            <th>Estado Mercado Pago</th>
                            <th>Método de pago</th>
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
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('admin.reportes.orders')}}',
                columns: [

                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'order_id', name: 'order_id', orderable: true, searchable: true },
                    { data: 'checkout_id', name: 'checkout_id', orderable: true, searchable: true },
                    { data: 'value', name: 'value', orderable: true, searchable: true  },
                    { data: 'status_shopify', name: 'status_shopify', orderable: true },
                    { data: 'status_mercadopago', name: 'status_mercadopago', orderable: true },
                    { data: 'payment_method_id', name: 'payment_method_id', orderable: true },
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },

            });

            $('#status').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('admin.reportes.orders.status')}}',
                columns: [

                    { data: 'number', name: 'number', orderable: true, searchable: false },
                    { data: 'status_shopify', name: 'status_shopify', orderable: true, searchable: true },
                    { data: 'status_mercadopago', name: 'status_mercadopago', orderable: true, searchable: true },
                    { data: 'payment_method_id', name: 'payment_method_id', orderable: true, searchable: true  },
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },

            });

        });



    </script>
@endpush