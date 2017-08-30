@extends('templates.dash')

@section('titulo','Busquedas Referidos')

@section('content')

    <section class="invoice">
        <div class="page-header no-breadcrumb font-header">Resultados:</div>
        <div class="panel panel-default">
        

        <div class="box">
            <div class="panel panel-default">
                <div class="panel-heading font-header">Listado</div>
                <div class="panel-body">

                    <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                        @if (isset($results))
                        <table data-order='[[ 0, "asc" ]]' id="referidos" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
                                <th>Email</th>
                                <th>Código referido</th>
                            </tr>
                            </thead>
                            <tbody>

                                    @foreach ($results as $result)
                                        <tr>
                                            <td class="text-left">{{$result['id']}}</td>
                                            <td class="text-left">{{$result['nombres']}}</td>
                                            <td class="text-left">{{$result['email']}}</td>
                                            <td class="text-left">{{$result['apellidos']}}</td>
                                        </tr>
                                    @endforeach


                            </tbody>
                        </table>
                        @endif
                            @if(isset($err))
                                <br>
                                {{$err}}
                                <br><br>
                            @endif
                        <div class="col-md-12">
                            <a class="btn btn-danger" href="{{route('admin.search')}}" role="button">Atras</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>


@endsection

@section('scripts')
    <script>

        $(function() {
            $('#referidos').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                processing: true,
                //buttons: [
                  //  'copy', 'csv', 'excel', 'pdf', 'print'
                //],
                
                "language": {
                    "url": "{{ asset('css/Spanish.json') }}"
                }
            });

        });

    </script>
@stop
