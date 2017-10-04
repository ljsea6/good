@extends('templates.dash')

@section('titulo', 'Gestion de Ordenes')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="panel-footer">
                <div class="alert alert-success alert-dismissable">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{ session()->get('success') }}</strong>
                </div>
            </div>
        @endif
            @if($order->fecha_compra == null)
                <h2>Comprar Orden</h2>
            @endif
        <div class="panel panel-danger">
            <div class="panel-heading">Orden {{ $order->tipo_orden }}</div>
            <div class="row">
                <div class="panel-body">
                    <form id="orden" class="form" action="/admin/orders/{{ $order->id }}" method="post">

                        <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                            <div class="form-group">
                                <label for="name" class="text-left">Número de Orden</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{$order->name}}" disabled>
                            </div>
                        </div>

                        @if ($order->fecha_compra != null)
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left">Fecha Compra</label>
                                    <input id="date" name="date" type='text' class="form-control" value="{{$order->fecha_compra}}" disabled/>
                                </div>
                            </div>
                        @endif

                        @if($order->fecha_compra != null && $order->tipo_orden != 'nacional' && $order->codigo_envio == null && $order->codigo_envio_internacional == null)

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Internacional</label>
                                    <input type="text" class="form-control" id="code_internacional" name="code_internacional" required>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="url" class="text-left">Url Envio</label>
                                    <input type="url" class="form-control" id="url" name="url" required>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <button type="submit" class="btn btn-danger text-left">Guardar</button>
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right" >Atrás</a>
                            </div>
                        @endif
                        @if($order->fecha_compra != null && $order->tipo_orden != 'nacional' && $order->codigo_envio == null && $order->codigo_envio_internacional != null)

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Internacional</label>
                                    <input type="text" class="form-control" id="code_internacional" name="code_internacional" value="{{$order->codigo_envio_internacional}}" disabled>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Nacional</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <button type="submit" class="btn btn-danger text-left">Guardar</button>
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right">Atrás</a>
                            </div>
                        @endif
                        @if($order->fecha_compra != null && $order->tipo_orden != 'nacional' && $order->codigo_envio != null && $order->codigo_envio_internacional == null )

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Internacional</label>
                                    <input type="text" class="form-control" id="code_internacional" name="code_internacional">
                                </div>
                            </div>
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Nacional</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{$order->codigo_envio}}" disabled>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="url" class="text-left">Url Envio</label>
                                    <input type="url" class="form-control" id="url" name="url" value="{{$order->url_envio}}">
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right">Atrás</a>
                            </div>
                        @endif
                        @if($order->fecha_compra != null && $order->tipo_orden != 'nacional' && $order->codigo_envio != null && $order->codigo_envio_internacional != null )

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Internacional</label>
                                    <input type="text" class="form-control" id="code_internacional" name="code_internacional" value="{{$order->codigo_envio_internacional}}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Nacional</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{$order->codigo_envio}}" disabled>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left">Url Envío</label>
                                    <input type="url" class="form-control" id="url" name="url" value="{{$order->url_envio}}" disabled>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right">Atrás</a>
                            </div>
                        @endif

                        @if($order->fecha_compra != null && $order->tipo_orden != 'nacional/internacional' && $order->tipo_orden != 'internacional' &&  $order->codigo_envio != null && $order->url_envio != null)

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Nacional</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{$order->codigo_envio}}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="url" class="text-left">Url Envio</label>
                                    <input type="url" class="form-control" id="url" name="url" value="{{$order->url_envio}}" disabled>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right">Atrás</a>
                            </div>

                        @endif
                        @if($order->fecha_compra != null && $order->tipo_orden != 'nacional/internacional' && $order->tipo_orden != 'internacional' && $order->codigo_envio == null && $order->url_envio == null)

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio Nacional</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <button id="submitButton" type="submit" class="btn btn-danger text-left">Guardar</button>
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right">Atrás</a>
                            </div>
                        @endif

                        @if($order->codigo_envio == null && $order->codigo_envio_internacional == null && $order->fecha_compra == null)
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="tipo_orden">Fecha de Compra:</label>
                                    <div class='input-group date' id='datetimepicker1'>

                                        <input required id="date" name="date" type='text' class="form-control" />
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tipo_orden">Tipo de Orden:</label>
                                    <select  class="form-control"name="tipo_orden" id="tipo_orden" required>

                                            @if ($order->tipo_orden == 'nacional')
                                                <option value=""></option>
                                                <option value="nacional" selected>Nacional</option>
                                                <option value="internacional">Internacional</option>
                                                <option value="nacional/internacional">Nacional/Internacional</option>
                                            @endif
                                            @if ($order->tipo_orden == 'internacional')
                                                <option value=""></option>
                                                <option value="nacional">Nacional</option>
                                                <option value="internacional" selected>Internacional</option>
                                                <option value="nacional/internacional">Nacional/Internacional</option>
                                            @endif
                                            @if ($order->tipo_orden == 'nacional/internacional')
                                                <option value=""></option>
                                                <option value="nacional">Nacional</option>
                                                <option value="internacional">Internacional</option>
                                                <option value="nacional/internacional" selected>Nacional/Internacional</option>
                                            @endif

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <button type="submit" class="btn btn-danger text-left">Comprar</button>
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right">Atrás</a>
                            </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@push('scripts')
    <script>
        $(document).ready(function(){
            $('#tipo_orden').select2({
                theme: "bootstrap",
                width: '100%',
                language: "es",
                placeholder: "Seleccionar Tipo...",
                allowClear: true
            });

            $(function () {
                $('#date').datetimepicker({
                    icons: {
                        time: "fa fa-clock-o",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down"
                    }
                });
            });



        });

    </script>
@endpush