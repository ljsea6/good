@extends('templates.dash')

@section('titulo', 'Reporte Códigos')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado</div>
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
                    
                    <table data-order='[[ 0, "asc" ]]' id="code" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Código</th>
                            <th>Referidos</th>
                            <th>Ordenes Referidos</th>
                            <th>Total Compras Referidos</th>
                            <th>Ganancias</th>
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
            var table = $('#code').DataTable({
               dom: 'Bfrtip',
               buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
               ],
               responsive: true,
               processing: true,
               serverSide: false,
               deferRender: true,
               pagingType: "full_numbers",
               ajax: '{{route('admin.reportes.code')}}',
               columns: [
                    
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: false },
                    { data: 'apellidos', name: 'apellidos', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: true, searchable: true },
                    { data: 'referidos', name: 'referidos', orderable: true, searchable: true },
                    { data: 'ordenes_referidos', name: 'ordenes_referidos', orderable: true, searchable: true },
                    { data: 'total_precio_ordenes_referidos', name: 'total_precio_ordenes_referidos', orderable: true, searchable: true },
                    { data: 'ganancias', name: 'ganancias', orderable: true, searchable: false },

                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },
                
            });
            

            

            
           
            
    });    
        
        
   
</script>
@endpush