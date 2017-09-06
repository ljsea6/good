
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

                    <table data-order='[[ 0, "asc" ]]' id="gifts" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Tercero</th>
                            <th>Código</th>
                            <th>Gift Card</th>
                            <th>Ordenes</th>
                            <th>Precio</th>
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
            $('#gifts').DataTable({
                responsive: true,
                processing: true,
                //serverSide: true,
                //deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('admin.gifts.data')}}',
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: true },
                    { data: 'customer', name: 'customer', orderable: true, searchable: true },
                    { data: 'code', name: 'code', orderable: true, searchable: true },
                    { data: 'gift', name: 'gift', orderable: true, searchable: true },
                    { data: 'order', name: 'order', orderable: true, searchable: true  },
                    { data: 'value', name: 'value', orderable: true, searchable: true  },
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },

            });

        });



    </script>
@endpush