@extends('templates.dash')

@section('titulo','Enviar invitación')

@section('content')
<div class="container">
   <div class="row">
       <div class="col col-md-6 col-md-offset-3"   >
           <div class="panel panel-default">
             <div class="panel-heading"><h3 class="panel-title">Envio de Códigos </h3></div>
             <div class="panel-body">
               {!! Form::open(['route' => 'admin.send', 'method' => 'post']) !!}
                 <div class="form-group text-left">
                     {!! Form::label('email', 'Email') !!}
                     {!! Form::email('email', null, ['class' => 'form-control' ]) !!}
                 </div>
                 <div class="form-group text-left">
                     {!! Form::label('code', 'Código') !!}
                     {!! Form::text('code', null, ['class' => 'form-control' ]) !!}
                 </div>
                 <div class="form-group text-left">
                     {!! Form::label('body', 'Mensaje') !!}
                     {!! Form::textarea('body', null, ['class' => 'form-control' ]) !!}
                 </div>
                 <div class="form-group text-left">
                     {!! Form::submit('Enviar', ['class' => 'btn btn-danger' ] ) !!}
                 </div>
               {!! Form::close() !!}
             </div>
           </div>
        </div>
   </div>
</div>
@endsection
