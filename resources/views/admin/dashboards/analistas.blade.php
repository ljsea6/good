@extends('templates.dash')

@section('titulo','Dashboard Analistas')

@section('content')
<section class="invoice">
<div class="page-header no-breadcrumb font-header"><i class="fa fa-truck"></i> Rol Analistas</div>  
 <div class="panel panel-default">
    <div class="panel-body">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
        </div>
        <!-- /.col -->
      </div>
      <div class="row">
        <div class="col-lg-3 col-xs-8">
          <div class="panel panel-default bg-info panel-stat no-icon">
                <div class="panel-body content-wrap">
                  <div class="value">
                    <h2 class="font-header no-m">20</h2>
                  </div>
                  <div class="detail text-right">
                    <div class="text-upper">Recogidas sin llegar</div>
                    <div class="text-muted m-d-2">Con mas de 24 horas</div>
                  </div>
                </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-8">
              <div class="panel panel-default bg-success panel-stat">
                <div class="panel-body">
                  <div class="value stat-icon">
                    <span><i class="icon-upload"></i></span>
                  </div>
                  <div class="detail text-right">
                    <h3 class="text-upper font-header no-m">44</h3>
                    <small class="text-upper">Ordenes por facturar</small>
                  </div>
                </div>
              </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-8">
              <div class="panel panel-default bg-purple panel-stat no-icon">
                <div class="panel-body content-wrap">
                  <div class="value">
                    <h2 class="font-header no-m">20</h2>
                  </div>
                  <div class="detail text-right">
                    <div class="text-upper">Ordenes por cerrar</div>
                    <div class="text-muted m-d-2">Clientes VIP</div>
                  </div>
                </div>
              </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-5">
        <div class="panel">
                <div class="panel-body">
                  <div class="row font-12">
                    <div class="col-xs-9">
                      <span class="text-upper font-header">Costo vs Facturación</span>
                      <span class="pointer m-d-1 m-l-10"><i class="icon-file-text2"></i></span>
                      <span class="pointer m-d-1 m-l-10 refresh-widget"><i class="icon-spinner9"></i></span>
                    </div>
                    <div class="col-xs-3 text-right">
                      <label class="switch-toggle m-l-5 switch-red">
                        <input type="checkbox">
                        <span></span>
                      </label>
                    </div>
                  </div>
                  <div class="chart-placeholder line-placeholder m-t-10"></div>
                </div>
        </div>
        </div>
        <div class="col-xs-5">
        <div class="panel">
                <div class="panel-body">
                  <div class="row font-12">
                    <div class="col-xs-6">
                      <span class="text-upper font-header">Ordenes cerradas</span>
                    </div><!-- /.col -->
                    <div class="col-xs-6 text-right">
                      <span class="pointer m-d-1 m-l-10"><i class="icon-download2"></i></span>
                      <span class="pointer m-d-1 m-l-10 refresh-widget"><i class="icon-spinner9"></i></span>
                    </div><!-- /.col -->
                  </div><!-- /.row -->

                  <div class="chart-placeholder area-placeholder m-t-10"></div>
                </div>
                <div class="loading-wrap">
                  <div class="loading-dots">
                    <div class="dot1"></div>
                    <div class="dot2"></div>
                  </div>
                </div>
              </div>   
       </div>
  </div></div>
</section>
@endsection

@section('scripts')
	<script type="text/javascript" src="{{ asset('js/Chart.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/dashboard2.js') }}"></script>
  <script type="text/javascript">
  var mainColor = '#5090F7';
        var secondaryColor = '#34495e';
    ////initialize all charts
        initChart();

        function initChart()  {        
          var data = [{ data: [[0, 5000], [1, 8000], [2, 5000], [3, 8000], [4, 7000], [5,9000], [6, 8000], [7, 8000], [8, 10000], [9, 12000], [10, 10000], [11, 5000], [12, 9000]],
            label: "Costo de ordenes"
          },
            { data: [[0, 8000], [1, 9000], [2, 12000], [3, 11000], [4, 13000], [5,9000], [6, 10000], [7, 9000], [8, 9000], [9, 8000], [10, 10000], [11, 12000], [12, 13000 ]],
            label: "Valor facturado de ordenes"
          }],

          options = { 
            xaxis: {
              ticks: [[1, 'Jan'], [2, 'Feb'], [3, 'Mar'], [4, 'Apr'], [5, 'May'], [6, 'Jun'], [7, 'Jul'], [8, 'Aug'], [9, 'Sep'], [10, 'Oct'], [11, 'Nov'], [12, 'Dec']]
            },
            series: {
              lines: {
                show: true, 
              },
              points: {
                show: true,
                radius: '3.5'
              },
              shadowSize: 0
            },
            grid: {
              hoverable: true,
              clickable: true,
              color: '#bbb',
              borderWidth: 1,
              borderColor: '#eee'
            },
            colors: [mainColor,secondaryColor]
          },
          plot;

          //Area Chart
          var areaData = [{ 
            data: [[0, 5], [1, 8], [2, 5], [3, 8], [4, 7], [5,9], [6, 8]],
              label: "Promedio Ordenes Cerradas ultimos 5 dias"
            }
          ],

          areaOptions = { 
            xaxis: {
              ticks: [[1, 'Mon'], [2, 'Tue'], [3, 'Wed'], [4, 'Thr'], [5, 'Fri'], [6, 'Sat'], [7, 'Sun']]
            },
            series: {
              lines: { 
                show: true, 
                fill: true
              },
              shadowSize: 0
            },
            grid: {
              hoverable: true,
              clickable: true,
              color: '#bbb',
              borderWidth: 1,
              borderColor: '#eee'
            },
            colors: [mainColor]
          },
          areaPlot;

          plot = $.plot($('.line-placeholder'), data, options);
          areaPlot = $.plot($('.area-placeholder'), areaData, areaOptions);

          $("<div id='tooltip'></div>").css({
            position: "absolute",
            display: "none",
            border: "1px solid #95a4b8",
            padding: "4px",
            "font-size": "12px",
            color: "#fff",
            "border-radius": "4px",
            "background-color": "#95a4b8",
            opacity: 0.90
          }).appendTo("body");

          $(".chart-placeholder").bind("plothover", function (event, pos, item) {
            var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
            $("#hoverdata").text(str);
          
            if (item) {
              var x = item.datapoint[0],
                y = item.datapoint[1];
              
                $("#tooltip").html(item.series.label+ " : " + y)
                .css({top: item.pageY+5, left: item.pageX+5})
                .fadeIn(200);
            } else {
              $("#tooltip").hide();
            }
          });

          //Sparkline
          $('.sparkbar').sparkline([15,19,20,22,33,27,31,27,19,30,21,10,15,18,25,9,11,14,16,10,15,16], {
            type: 'bar', 
            barColor: mainColor
          });
          $('.sparkline').sparkline([15,19,20,22,33,27,31,27,19,30,21,10,15,18,25,9,11,14,16,10,15,16,7,12,16,17,14,9,2,12,15,16,7,12,15,12,9,5,7,19,11,20,12,12], {
            type: 'line', 
            lineColor: mainColor,
            fillColor: false,
            maxSpotColor: false,
            minSpotColor: false,
            spotRadius: 0,
            highlightLineColor: secondaryColor
          });
        }
  </script>>
@stop