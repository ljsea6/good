@extends('templates.dash')

@section('titulo', 'Listado de comisiones')

@section('content')
    <div class="box">
    <div class="panel panel-default">
        <div class="panel-heading font-header">Listado de comisiones</div>
        <div class="panel-body">
            {!! Alert::render() !!}
            <a href="{{ route('admin.comisiones.create') }}" class="btn btn-primary">Nueva comisión</a>
            <br><br>
            <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                <table data-order='[[ 0, "asc" ]]' id="tabla_comisiones" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                <thead>
                    <tr>
                       <th>Numero de comision</th>
                       <th>id_tercero</th>
                       <th>Numero de transsaccion</th>
                        <th>Nombre de regla</th>
                         <th>Usuario</th>
                          <th>Fecha</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
$(function() { 
  
$('#tabla_comisiones').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pagingType: "full_numbers",
            ajax: '{!! route('comisiones.data') !!}',
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false },
                { data: 'identificacion', name: 'identificacion', orderable: false, searchable: false },
                { data: 'id_transaccion', name: 'id_transaccion', orderable: false },
                { data: 'regla', name: 'cantidad', orderable: false, searchable: false },
                 { data: 'usuario', name: 'profundidad', orderable: false },
                { data: 'fecha', name: 'profundidad', orderable: false },
                 { data: 'valor', name: 'profundidad', orderable: false },
                  { data: 'estado', name: 'profundidad', orderable: false },
                { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
            ],
            
        





            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });
    });

</script>
@endsection