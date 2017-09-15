@extends('templates.dash')

@section('titulo', 'Listado de roles')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <b>Listado de roles</b>
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
            <a href="{{ route('admin.perfiles.create') }}" class="btn btn-primary">
                Nuevo Perfil
            </a>
            <br/>
            <br/>
            <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                <table data-order='[[ 0, "asc" ]]' id="tabla_perfiles" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                    <thead>
                        <tr>
                            <th>
                                ID
                            </th>
                            <th>
                                Nombre
                            </th>
                            <th>
                                Descripción
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
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    {!! Form::select('permisos', $permisos, null, ['id' => 'select-permisos', 'multiple' => true]) !!}
                                </div>
                            </div>

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
        $('#tabla_perfiles').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pagingType: "full_numbers",
            ajax: '{!! route('roles.data') !!}',
            columns: [
                { data: 'id', name: 'id', orderable: false, searchable: false },
                { data: 'name', name: 'roles.name'},
                { data: 'description', name: 'roles.description' },
                { data: 'permisos', name: 'editar', orderable: false, searchable: false, className: "centrar"},
                { data: 'action', name: 'editar', orderable: false, searchable: false, className: "centrar"}
            ],
            
            "language": {
                "url": "{{ asset('css/Spanish.json') }}"
            }
        });

        $('#select-permisos').multiSelect({
            selectableHeader: "<div class='text-center'><b>Permisos no asignados</b></div>",
            selectionHeader: "<div class='text-center'><b>Permisos asignados</b></div>",
            afterSelect: function(value) {
                $.ajax({
                    url: '{{ route('asignar.permisos') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        seccion: 'perfiles',
                        permission_id: value[0],
                        role_id: rol_id
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
                        seccion: 'perfiles',
                        permission_id: value[0],
                        role_id: rol_id
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
        
    });
    
    var rol_id = null;

    function get_permisos(id) {
            rol_id = id;
            //$('#select-permisos option').attr('selected', false);
            //rol_id = $(this).attr('rol_id');
            $.ajax({
                url: '{{ route('get.permisos') }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    seccion: 'perfiles',
                    id: rol_id
                }
            }).done(function(data) {
                $.each(data, function(index) {
                    //alert(index);
                    $('#select-permisos option[value="' + index + '"]').attr('selected', true);
                });
                $('#select-permisos').multiSelect('refresh');
            });
        }
</script>
@endsection