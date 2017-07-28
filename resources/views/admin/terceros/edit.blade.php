@@extends('templates.dash')

@section('titulo','Editar Tercero')

@section('content')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="stepwizard">
                <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step">
                        <a href="#step-1"  class="btn btn-primary btn-circle">1</a>
                        <p>Paso 1</p>
                    </div>
                </div>
            </div>
            <form action="{{route('admin.terceros.update', $tercero->id)}}" method="post" class="form-horizontal">
                <div class="row setup-content" id="step-1">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <h3 class="text-center">Editar Tercero</h3>
                            <hr>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <label for="name">Nombres</label>
                                    <input id="name" name="name" type="text" class="form-control" value="{{$tercero->nombres}}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <label for="apellidos">Apellidos</label>
                                    <input id="apellidos" name="apellidos" type="text" class="form-control" value="{{$tercero->apellidos}}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <label for="email">Email</label>
                                    <input id="email" name="email" type="email" class="form-control" value="{{$tercero->email}}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <label>Estado</label><br>
                                   <select id="state" name="state" class="selectpicker">
                                        @if($tercero->state == true)
                                            <option selected value="true">Activo</option>
                                            <option value="false">Inactivo</option>
                                        @endif
                                         @if($tercero->state == false)
                                            <option value="true">Activo</option>
                                            <option selected value="false">Inactivo</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#myModal">Editar</button>
                                </div>
                            </div>

                            <!-- Modal -->
                              <div class="modal fade" id="myModal" role="dialog">
                                <div class="modal-dialog modal-md">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      <h4 class="modal-title" style="color:#f60620;">¿Realmente está seguro?</h4>
                                    </div>
                                    <div class="modal-body">
                                      <p class="text-center">¡Deberá tener en cuenta qué al desactivar un tercero este no podrá volver a ser activado!</p>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
                                      <button class="btn btn-danger" type="submit">Editar</button>
                                    </div>
                                  </div>
                                </div>
                              </div>

                            @if ($errors->any())
                                <div class="alert alert-danger fade in col-sm-offset-3 col-sm-6">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection




