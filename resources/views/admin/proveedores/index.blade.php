@extends('templates.dash')

@section('titulo', 'Listado de usuarios')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <b>Listado de proveedores</b>
        <ul class="panel-toolbar list-unstyled">
            <li class="dropdown collapse-option">
                <a href="#" class="text-muted dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-ellipsis-h">
                    </i>
                </a>
                <ul class="dropdown-menu dropdown-animated fade-effect closed">
                    <li>
                        <a href="#" class="text-muted refresh-widget">
                            <i class="icon-spinner9">
                            </i>
                        </a>
                    </li>
                    <li>
                        <a href="#collapsedPanel" class="expand-widget" data-toggle="collapse" aria-expanded="true">
                            <i class="icon-circle-up">
                            </i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="fullscreen-widget">
                            <i class="icon-enlarge">
                            </i>
                        </a>
                    </li>
                    <li>
                        <a>
                            <i class="icon-cross">
                            </i>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div id="collapsedPanel" class="collapse in">
        <div class="panel-body">
            {!! Alert::render() !!}
            <a href="{{ route('admin.proveedores.create') }}" class="btn btn-primary">
                Nuevo Proveedor
            </a>
            <br/>
            <br/>
            <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                <table data-order='[[ 0, "asc" ]]' id="tabla_usuarios" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                <thead>
                    <tr>
                        <th>
                            Avatar
                        </th>
                        <th>
                            Identificación
                        </th>
                        <th>
                            Nombres
                        </th>
                        <th>
                            Apellidos
                        </th>
                        <th>
                            Dirección
                        </th>
                        <th>
                            Ciudad
                        </th>
                        <th>
                            Rol
                        </th>
                        <th>
                            Empresa
                        </th>
                        <th>
                            Permisos
                        </th>
                        <th>
                            Acciones
                        </th>
                    </tr>
                </thead>
                </table>
            </div>
            <div class="modal" id="permisos">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                                <span class="sr-only">
                                    Cerrar
                                </span>
                            </button>
                            <h4 class="modal-title">
                                Gestionar permisos
                            </h4>
                        </div>
                        <div class="modal-body" style="position: relative; margin: 0 auto; width: 70%;">
                         {!! Form::select('permisos', $permisos, null, ['id' => 'select-permisos','class' => 'form-control', 'multiple']) !!}
                        </div>
                        <div class="modal-footer">
                            <a href="#" data-dismiss="modal" class="btn btn-danger">
                                Cerrar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="loading-wrap">
        <div class="loading-dots">
            <div class="dot1">
            </div>
            <div class="dot2">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).on('ready', function() {
        $('#select-permisos').multiSelect({
            selectableHeader: "<div class='text-center'><b>Permisos no asignados</b></div>",
            selectionHeader: "<div class='text-center'><b>Permisos asignados</b></div>",
            afterSelect: function(value) {
                $.ajax({
                    url: '{{ route('asignar.permisos') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        seccion: 'terceros',
                        permission_id: value[0],
                        tercero_id: tercero_id
                    }
                }).done(function(data) {
                    noty({
                        layout: 'topRight',
                        text: data,
                        theme: 'flat-noty',
                        type: 'success',
                        timeout: '2000'
                    });
                });
            },
            afterDeselect: function(value) {
                $.ajax({
                    url: '{{ route('desasignar.permisos') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        seccion: 'terceros',
                        permission_id: value[0],
                        tercero_id: tercero_id
                    }
                }).done(function(data) {
                    noty({
                        layout: 'topRight',
                        text: data,
                        theme: 'flat-noty',
                        type: 'danger',
                        timeout: '2000'
                    });
                });
            }
        });
        
         $('#tabla_usuarios').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pagingType: "full_numbers",
            ajax: '{!! route('Proveedores.data') !!}',
            columns: [
                { data: 'avatar', name: 'avatar', orderable: false, searchable: false },
                { data: 'identificacion', name: 'terceros.identificacion', orderable: false },
                { data: 'nombres', name: 'terceros.nombres'},
                { data: 'apellidos', name: 'terceros.apellidos'},
                { data: 'direccion', name: 'terceros.direccion'},
                { data: 'ciudad', name: 'ciudad', orderable: false, searchable: false, className: "centrar" },
                { data: 'rol', name: 'rol', orderable: false, searchable: false, className: "centrar" },
                { data: 'empresa', name: 'empresa', orderable: false, searchable: false, className: "centrar" },
                { data: 'permisos', name: 'permisos', orderable: false, searchable: false, className: "centrar" },
                { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
            ],
            
            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });
    });

    var tercero_id = null;

    function get_permisos (id) {
            $('#select-permisos option').attr('selected', false);
            tercero_id = id;
            $.ajax({
                url: '{{ route('get.permisos') }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    seccion: 'terceros',
                    id: tercero_id
                }
            }).done(function(data) {
                $.each(data, function(index) {
                    $('#select-permisos option[value="' + index + '"]').attr('selected', true);
                    //alert($('#select-permisos option[value="' + index + '"]').attr('selected'));
                });
                $('#select-permisos').multiSelect('refresh');
            });
    }
</script>
@endsection
