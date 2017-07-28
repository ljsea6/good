@extends('templates.dash')

@section('titulo', 'Listado de Ordenes')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Ordenes</div>
            <div class="panel-body">

                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "asc" ]]' id="tabla_productos" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Telefono</th>
                            <th>Email</th>
                            <th>Precio Total</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($orders as $order)
                            <tr>
                                <td>{{$order->id}}</td>
                                <td>{{$order->billing_address['first_name']}}</td>
                                <td>{{$order->billing_address['address1']}}</td>
                                <td>{{$order->billing_address['phone']}}</td>
                                <td>{{$order->email}}</td>
                                <td>$ {{number_format($order->total_price, 2)}}</td>
                                <td>{{$order->financial_status}}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
@push('scripts')
<script>
    $(function() {
        var table = $('#tabla_productos').DataTable({
            serverSide: false,
            deferRender: true,
            processing: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],

            /*"columns": [
             { "products": "" },
             { "products": "title" },
             { "products": "product_type" }
             ],*/
            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });
    });

</script>
@endpush