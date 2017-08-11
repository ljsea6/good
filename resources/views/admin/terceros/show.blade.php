@extends('templates.dash')

@section('titulo', 'Listado de Terceros Hijos')
@foreach($send['networks'] as $network)
@section('content')

        <div class="box">
            <div class="panel panel-default">
                <div class="panel-heading font-header">Referidos {{$network['name']}}</div>
                <div class="panel-body">

                    <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                        <table data-order='[[ 0, "asc" ]]' id="{{$network['name']}}" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
                                
                                <th>Email</th>
                            </tr>
                            </thead>
                            <tbody>
                                    @foreach($send['referidos'] as  $referido)
                                    <tr>
                                        <td class="text-left">{{$referido['id']}}</td>
                                        <td class="text-left">{{strtolower($referido['nombres'])}}</td>
                                        
                                        <td class="text-left">{{strtolower($referido['email'])}}</td>
                                    </tr>
                                   @endforeach           
                            </tbody>
                        </table>
                        
                        <div class="col-md-12">
                            <a class="btn btn-danger" href="{{route('admin.terceros.index')}}" role="button">Atras</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@stop
@push('scripts')
<script>
    $(function() {
        $('#{{$network['name']}}' ).DataTable({
            //dom: 'Bfrtip',
            responsive: true,
            processing: true,
          //buttons: [
          //     'copy', 'csv', 'excel', 'pdf', 'print'
          //  ],
                
            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });
    });
</script>
@endpush
@endforeach