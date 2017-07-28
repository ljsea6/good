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
                            <th>Título</th>
                            <th>Tipo</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($productsFinder as $product)
                                <tr>
                                    <td>{{$product['id']}}</td>
                                    <td>{{$product['title']}}</td>
                                    <td>{{$product['product_type']}}</td>
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
            serverSide:false,
            deferRender: true,
            processing: false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],

            "columns": [
                { "products": "#" },
                { "products": "title" },
                { "products": "product_type" }
            ],
            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });
    });

</script>
@endpush