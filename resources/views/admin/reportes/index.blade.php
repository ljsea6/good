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
                <button type="submit">Oye</button>
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    
                    <table data-order='[[ 0, "asc" ]]' id="terceros" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombres</th>
                            <th>Total Compras Referidos</th>
                            <th>%</th>
                            <th>Ganancia</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado Reporte Ordenes sin productos relacionados</div>
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
                    <table data-order='[[ 0, "asc" ]]' id="ordenes" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
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
            var editor = new $.fn.dataTable.Editor( {
                ajax: {
                    create: {
                        type: 'POST',
                        url:  '../php/rest/create.php'
                    },
                    edit: {
                        type: 'PUT',
                        url:  '../php/rest/edit.php?id=_id_'
                    },
                    remove: {
                        type: 'DELETE',
                        url:  '../php/rest/remove.php?id=_id_'
                    }
                },
                table: "#terceros",
                fields: [ {
                        label: "First name:",
                        name: "first_name"
                    }, {
                        label: "Last name:",
                        name: "last_name"
                    }, {
                        label: "Position:",
                        name: "position"
                    }, {
                        label: "Office:",
                        name: "office"
                    }, {
                        label: "Extension:",
                        name: "extn"
                    }, {
                        label: "Start date:",
                        name: "start_date"
                    }, {
                        label: "Salary:",
                        name: "salary"
                    }
                ]
            } );

            
            var table = $('#terceros').DataTable({
               dom: 'Bfrtip',
               buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
               responsive: true,
               processing: true,
               serverSide: true,
               deferRender: true,
               pagingType: "full_numbers",
               ajax: '{{route('admin.reportes.datos')}}',
               columns: [
                    
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true },
                    { data: 'total', name: 'total', orderable: true, searchable: true  },
                    { data: 'porcentaje', name: 'porcentaje', orderable: false },
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
            
            
            
            $('#ordenes').DataTable({
               dom: 'Bfrtip',
               buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
               responsive: true,
               processing: true,
               serverSide: true,
               deferRender: true,
               pagingType: "full_numbers",
               ajax: '{{route('admin.reportes.datos.orders')}}',
               columns: [
                    
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                   
                    
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },
                
            });
            
            
           
            
    });    
        
        
   
</script>
@endpush