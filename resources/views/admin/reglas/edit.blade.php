@extends('templates.dash')

@section('titulo','editar regla')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                    <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                    <p>Paso 1</p>
                </div>
            </div>
        </div>
        {!! Form::model($reglas,['route' => ['admin.reglas.update' , $reglas->id], 'method' => 'PUT']) !!}
       
            <div class="row setup-content" id="step-1">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3> Datos básicos : </h3>
                        <hr>
                          <div class="row">
                           <div class="col-md-6">
                        {!! Field::select('id_red', $id_red->toarray() , ['required']) !!}
                          </div>
                        <div class="col-md-6">
                        {!! Field::number('nivel', ['ph' => 'nivel','label'=>'Nivel' , 'required']) !!}
                        </div>
                         <div class="col-md-6">
                            {!! Field::number('valor', ['ph' => 'valor' ,'label' => 'Valor' , 'required']) !!}
                            </div>
                             <div class="col-md-6">
                            {!! Field::text('estado', ['ph' => 'estado' ,'label' => 'Estado' , 'required']) !!}
                            </div>
                              <div class="col-md-6">
                            {!! Field::text('plataforma', ['ph' => 'plataforma' ,'label' => 'Plataforma' , 'required']) !!}
                            </div>
                         </div>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="submit" >Guardar</button>
                    </div>
                </div>
            </div>
           
        {!! Form::close() !!}
    </div>
</div>
@endsection

