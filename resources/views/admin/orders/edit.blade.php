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
        @if(isset($product->tipo_producto) && count($product->tipo_producto) > 0 && $product->tipo_producto == 'nacional')
                <h2>Envio Nacional</h2>
            @endif
            @if(isset($product->tipo_producto) && count($product->tipo_producto) > 0 &&$product->tipo_producto == 'internacional')
                <h2>Envio Internacional</h2>
            @endif

        <div class="panel panel-danger">
            <div class="panel-heading">Orden</div>
            <div class="row">
                <div class="panel-body">
                    <form class="form" action="/admin/orders/{{ $order->id }}" method="post">

                        <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                            <div class="form-group">
                                <label for="name" class="text-left"># orden</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{$order->name}}" disabled>
                            </div>
                        </div>

                        @if($order->fecha_compra != null && $order->codigo_envio != null )
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left">Fecha Compra</label>
                                    <input id="date" name="date" type='text' class="form-control" value="{{$order->fecha_compra}}" disabled/>
                                </div>
                            </div>
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{$order->codigo_envio}}" disabled>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right">Atrás</a>
                            </div>
                        @endif

                        @if($order->fecha_compra != null && $order->codigo_envio == null )
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left">Fecha Compra</label>
                                    <input id="date" name="date" type='text' class="form-control" value="{{$order->fecha_compra}}" disabled/>
                                </div>
                            </div>
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <label for="code" class="text-left"># Códido de Envio</label>
                                    <input type="text" class="form-control" id="code" name="code">
                                </div>
                            </div>

                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <button id="submitButton" type="submit" class="btn btn-danger text-left">Guardar</button>
                                <a href="{{route('admin.orders.home')}}" class="btn btn-danger text-right">Atrás</a>
                            </div>
                        @endif

                        @if($order->fecha_compra == null && $order->codigo_envio == null )
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <input required id="date" name="date" type='text' class="form-control" />
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 text-left">
                                <div class="form-group">
                                    <select id="tipo" name="tipo" class="js-example-basic-single" style="width: 100%">
                                        <option value="nacional">Nacional</option>
                                        <option value="internacional">Internacional</option>
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

            $(".js-example-basic-single").select2({});

            $(function () {
                $('#datetimepicker1').datetimepicker({
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