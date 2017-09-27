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
                            <th>Producto</th>
                            <th>Unidades Vendias</th>
                            <th>Ventas</th>

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
                    { data: 'unidades_vendidas', name: 'unidades_vendidas', orderable: true, searchable: true },
                    { data: 'ventas', name: 'ventas', orderable: true, searchable: true },

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