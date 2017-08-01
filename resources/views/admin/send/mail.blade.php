@extends('templates.dash')

@section('titulo','Enviar invitación')

@section('content')
<div class="container">
   <div class="row">
       <div class="col col-md-6 col-md-offset-3"   >
           <div class="panel panel-default">
             <div class="panel-heading"><h3 class="panel-title">Envio de Códigos </h3></div>
             <div class="panel-body">
                 <form action="{{route('admin.send')}}" method="post">
                    {{ csrf_field() }}
                    <div class="form-group text-left">
                        <label for="email">Email</label>
                        <input id="email" name="email" class="form-control" type="email">
                    </div>
                    <div class="form-group text-left">
                        <label for="emailone">Email</label>
                        <input id="email" name="emailone" class="form-control" type="email">
                    </div>
                    <div class="form-group text-left">
                        <label for="emailtwo">Email</label>
                        <input id="email" name="emailtwo" class="form-control" type="email">
                    </div>
                    <div id="buildyourform">
                        
                    </div>
                    <div class="form-group text-right">
                        <a class="btn btn-danger" id="add" style="bottom: 47.7px; position: relative">
                            <span class="glyphicon glyphicon-plus" ></span> 
                        </a>
                    </div>
                    <input type="hidden" id="code" name="code" value="{{currentUser()->email}}">
                    <div class="form-group text-left">
                        <label for="body">Mensaje</label>
                        <input id="body" name="body" class="form-control" value="Ahorra comprando, gana invitando con Hello. Mi código es: {{currentUser()->email}}">
                    </div>
                    <div class="form-group text-left">
                        <button class="btn btn-danger" type="submit">Enviar</button>
                    </div>
                 </form>
             </div>
           </div>
        </div>
   </div>
</div>
@endsection

@section('scripts')
<script>
     $("#add").click(function() {
        var intId = $("#buildyourform div").length + 1;
        if(intId < 8) {
            var fieldWrapper = $("<div class=\"form-group text-left\" />");
            var la = ("<label for=\"email" + intId + "\"> Email</label>");
            var fName = $("<input style=\"position: relative;\" id=\"email" + intId + "\" name=\"email" + intId + "\" type=\"email\" class=\"form-control\" />");
            var fType = $("<br>");
            var removeButton = $("<a type=\"button\" class=\"btn btn-danger\"> <span class=\"glyphicon glyphicon-minus\" ></span>  </a>");

            removeButton.click(function() {
                $(this).parent().remove();
            });
            fieldWrapper.append(la);
            fieldWrapper.append(fName);
            fieldWrapper.append(fType);
            fieldWrapper.append(removeButton);
            $("#buildyourform").append(fieldWrapper);
        }
        
    });
</script>
@endsection