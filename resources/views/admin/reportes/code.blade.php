@extends('templates.dash')

@section('titulo', 'Reporte Códigos')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado Usuarios con Last Name direfentes en Adreesses</div>
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
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Código</th>
                            <th>Tus referidos</th>
                            <th>Ordenes de tus referidos</th>
                            <th>Valor compras de tus referidos</th>
                            <th>Tus ganancias</th>
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
                    
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'last_name', name: 'last_name', orderable: true, searchable: true },
                    { data: 'code', name: 'code', orderable: true, searchable: true },
                    { data: 'numero_referido', name: 'numero_referido', orderable: true, searchable: true  },
                    { data: 'numero_ordenes_referidos', name: 'numero_ordenes_referidos', orderable: true },
                    { data: 'total_price_orders', name: 'total_price_orders', orderable: true },
                    { data: 'ganacias', name: 'ganacias', orderable: true },
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },
                
            });
            
            table.$('tr').click(function() {
                var data = table.row(this).data()[5];
                console.log('hola');
            });
            
            
            
            $('#productos').DataTable({
               //dom: 'Bfrtip',
               //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
                //],
               responsive: true,
               processing: true,
               serverSide: true,
               deferRender: true,
               pagingType: "full_numbers",
               ajax: '{{route('admin.reportes.datos.products')}}',
               columns: [
                    
                    { data: 'id', name: 'id', orderable: true, searchable: true },
                    { data: 'title', name: 'title', orderable: true, searchable: true },
                   
                    
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },
                
            });
            
            
           
            
    });    
        
        
   
</script>
@endpush