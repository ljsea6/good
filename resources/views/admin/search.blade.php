@extends('templates.dash')

@section('titulo','Buscar Referido')

@section('content')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="stepwizard">
                <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step">
                        <a href="#step-1"  class="btn btn-primary btn-circle">1</a>
                        <p>Buscar</p>
                    </div>
                </div>
            </div>
            <form action="{{route('admin.finder')}}" method="post" class="form-horizontal">
                <div class="row setup-content" id="step-1">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <h3 class="text-center">Buscar Referidos</h3>
                            <hr>
                            <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6">
                                    <label for="email">Ingresar correo</label>
                                    <input id="email" name="email" type="email" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6 text-left">
                                    <button class="btn btn-primary text-left" type="submit" >Buscar</button>
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

