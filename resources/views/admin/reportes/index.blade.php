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
                    
                    <table data-order='[[ 0, "asc" ]]' id="terceros" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombres</th>
                            <th>Código</th>
                            <th>Total Compras Referidos</th>
                            <th>Ganancia</th>
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
            var table = $('#terceros').DataTable({
               //dom: 'Bfrtip',
               //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
               responsive: true,
               processing: true,
               serverSide: true,
               deferRender: true,
               pagingType: "full_numbers",
               ajax: '{{route('admin.reportes.datos')}}',
               columns: [
                    
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: true, searchable: true },
                    { data: 'total', name: 'total', orderable: true, searchable: true  },
                    { data: 'ganancia', name: 'ganancia', orderable: true },
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },
                
            });
            
            table.$('tr').click(function() {
                var data = table.row(this).data()[5];
                console.log('hola');
            });

    });    
        
        
   
</script>
@endpush