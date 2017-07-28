@extends('templates.dash')

@section('titulo','Inicio')

@section('styles')
    {!! Html::style('css/jquery.fileupload.css') !!}
    {!! Html::style('css/jquery.fileupload-ui.css') !!}
    <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
@endsection

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                    <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                    <p>Paso 1</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                    <p>Paso 2</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                    <p>Paso 3</p>
                </div>
            </div>
        </div>

        {!! Form::open(['route' => ['admin.proveedores.store'] , 'method' => 'POST' , 'files' => true ,'id' =>'fileupload' ]) !!}
            <div class="row setup-content" id="step-1">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3> Datos básicos : </h3>
                        <hr>
                        <div class="avatarWrapper">
                            <div class="avatar">
                                <div class="uploadOverlay">
                                    <i class="fa fa-cloud-upload"></i>
                                </div>
                                <input type='file' id="avatar" name="avatar" />
                                <img id="target" src="{{ asset('img/avatar-bg.png') }}" alt="Avatar" />
                            </div>
                            @if ($errors->has('avatar'))
                                {!! $errors->first('avatar', '<span class="help-block" style="width: 200px; color: red;">:message</span>') !!}
                            @endif
                        </div>

                          <div class="row">
                        <div class="col-md-6">
                        {!! Field::number('identificacion', ['ph' => 'Identificacion' ,'label'=>'Nit' , 'required']) !!}
                         </div>
                         <div class="col-md-6">
                        {!! Field::select('tipo_id', $tipos->toarray() , ['required']) !!}
                          </div>
                          </div>
                        <div class="row">
                            <div class="col-md-6">
                                {!! Field::text('nombres', ['ph' => 'Nombre del proveedor', 'required']) !!}
                            </div>
                             <div class="col-md-6">
                                {!! Field::text('direccion', ['ph' => 'Direccion' ,'label' => 'Dirección' , 'required']) !!}
                            </div>
                        </div>
                        <div class="row">
                           
                            <div class="col-md-4">
                                {!! Field::text('telefono', ['ph' => 'Telefono' ,'label' => 'Teléfono', 'required','maxlength' => '10', 'OnKeyPress' => 'return event.charCode >= 48 && event.charCode <= 57'  ]) !!}
                            </div>
                            <div class="col-md-4">
                                {!! Field::text('celular', array('placeholder' => 'Celular','maxlength' => 10, 'OnKeyPress' => 'return event.charCode >= 48 && event.charCode <= 57' )) !!}
                            </div>  
                            <div class="col-md-4">
                                {!! Field::email('email', ['ph' => 'Email' , 'required']) !!}
                            </div>
                        </div>
                        {!! Field::select('ciudad_id', $ciudades->toArray(), ['required']) !!}
                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Siguiente</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content forma" id="step-2">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3> Creacion de proveedor : </h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                {!! Field::text('usuario', ['ph' => 'Usuario' , 'required']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! Field::password('contraseña', ['ph' => '********' , 'required']) !!}
                            </div>
                        </div>
                       
                        <div class="row">
                            
                        </div>
                        
                       
                   

                        <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Anterior</button>

                        <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Siguiente</button>  
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-3">
                <div class="col-xs-12">
                    <div class="box">
                        <!--<form id="fileupload" action="/Domina/public/uploads/recived_files/" method="POST" enctype="multipart/form-data">-->
                        <div id="fileupload" action="{{ route('uploads_init') }}">
                            <div class="row fileupload-buttonbar">
                                <div class="col-lg-7">
                                    <!-- The fileinput-button span is used to style the file input field as button -->
                                    <span class="btn btn-info fileinput-button">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <span>Agregar archivos...</span>
                                        <input type="file" name="files[]" multiple>
                                    </span>
                                    <!-- The global file processing state -->
                                    <span class="fileupload-process"></span>
                                </div>
                                <!-- The global progress state -->
                                <div class="col-lg-5 fileupload-progress fade">
                                    <!-- The global progress bar -->
                                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                    </div>
                                    <!-- The extended global progress state -->
                                    <div class="progress-extended">&nbsp;</div>
                                </div>
                            </div>
                            <!-- The table listing the files available for upload/download -->
                            <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
                        </div>
                        <br>
                    </div>
                    <!-- The blueimp Gallery widget -->
                    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
                        <div class="slides"></div>
                        <h3 class="title"></h3>
                        <a class="prev">‹</a>
                        <a class="next">›</a>
                        <a class="close">×</a>
                        <a class="play-pause"></a>
                        <ol class="indicator"></ol>
                    </div>
                     <button class="btn btn-success btn-lg pull-right guardar" type="submit">Guardar</button>  
                    </div>
                </div>
            </div>
        <br>
</div>
</div>
</div>

            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('scripts')


<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function (e) {
                $('#target').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#avatar").change(function(){
        readURL(this);
    });
    $('#ciudad_id').select2({
                language: "es",
                placeholder: "Seleccionar ciudad...",
                allowClear: true
    });
     $('#tipo_id').select2({
                language: "es",
                placeholder: "Seleccionar tipo...",
                allowClear: true
    });


    
</script>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Procesando...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Iniciar</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancelar</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Borrar</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancelar</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>

<script language='JavaScript'>
   var ruta_inicio="{{ route('uploads_init') }}";
</script>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
{!! Html::script('js/jquery.ui.widget.js') !!}
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
{!! Html::script('js/jquery.iframe-transport.js') !!}
<!-- The basic File Upload plugin -->
{!! Html::script('js/jquery.fileupload.js') !!}
<!-- The File Upload processing plugin -->
{!! Html::script('js/jquery.fileupload-process.js') !!}
<!-- The File Upload image preview & resize plugin -->
{!! Html::script('js/jquery.fileupload-image.js') !!}
<!-- The File Upload audio preview plugin -->
{!! Html::script('js/jquery.fileupload-audio.js') !!}
<!-- The File Upload video preview plugin -->
{!! Html::script('js/jquery.fileupload-video.js') !!}
<!-- The File Upload validation plugin -->
{!! Html::script('js/jquery.fileupload-validate.js') !!}
<!-- The File Upload user interface plugin -->
{!! Html::script('js/jquery.fileupload-ui.js') !!}
<!-- The main application script -->
{!! Html::script('js/main.js') !!}
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->






@endsection

