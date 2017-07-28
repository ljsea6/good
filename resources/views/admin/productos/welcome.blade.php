@extends('templates.dash')

@section('titulo', 'Listado de productos')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de productos</div>
            <div class="panel-body">
                {!! Alert::render() !!}
                <a href="{{ route('admin.productos.create') }}" class="btn btn-primary">Nuevo producto</a>
                <br><br>
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "asc" ]]' id="tabla_productos" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Email</th>
                            <th>Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{$product['id']}}</td>
                                <td>{{$product['first_name']}}</td>
                                <td>{{$product['last_name']}}</td>
                                <td>{{$product['email']}}</td>
                                <td>{{$product['created_at']}}</td>
                            </tr>
                        @endforeach
                        </tbody>

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
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            serverSide:false,
            deferRender: true,
            processing: false,
            "columns": [
                { "products": "id" },
                { "products": "first_name" },
                { "products": "last_name" },
                { "products": "email" },
                { "products": "created_at" }
            ],
            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });
    });

</script>
@endpush