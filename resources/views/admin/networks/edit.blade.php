@@extends('templates.dash')

@section('titulo','Editar red')

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
            <form action="{{route('admin.networks.update', $network->id)}}" method="post" class="form-horizontal">
                <div class="row setup-content" id="step-1">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <h3 class="text-center">Editar Red</h3>
                            <hr>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <label for="name">Nombre</label>
                                    <input id="name" name="name" type="text" class="form-control" value="{{$network->name}}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <label for="frontal_amount">Cantidad Frontal</label>
                                    <input id="frontal_amount" name="frontal_amount" type="number" class="form-control" value="{{$network->frontal_amount}}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <label for="depth">Profundidad</label>
                                    <input id="depth" name="depth" type="number" class="form-control" value="{{$network->depth}}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <button class="btn btn-primary" type="submit" >Guardar</button>
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




