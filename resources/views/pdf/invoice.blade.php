<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura</title>
    {!! Html::style('css/pdf.css') !!}
  </head>
  <body>
    <main>
     <table border=0>
       <tr>
          <th width="20%">
            <a href='http://www.domina.com.co'>
            <img src='img/logo_color.png' width=150>
            </th>
            <th align=center width="30%"><font size=1>Licencia Min. Comunicaciones<br>
                NIT. 800.088.155-3<br> 
                Resolución 001772 Sep. 07 de 2010<br>       
                Autoriza  Resol. DIAN No. 110000601100  de 2014/10/21 <br>               
                Rango: del CMC 15001 al CMC 20000             REGIMEN COMUN.<br>     
                Somos Grandes Contribuyentes según resolución 41 de enero de 2014.
                <br>Agentes Retenedores de Iva.</font>
            </th>
            <th width="30%">
                <div id="invoice">FACTURA<br><b>{{ $invoice['numero'] }}</b></div><br>
                <div class="date">{{ $date }}</div>
            </th>
       </tr>
      </table>
      <table width=80%>
           <tr><th align=left>Cliente : </th><th align=left>{{ $invoice['cliente'] }}</th>
               <th align=left>Identificación : </th><th align=left>{{ $invoice['identificacion'] }}</th></tr>
           <tr><th align=left>Dirección : </th><th align=left>{{ $invoice['direccion'] }}</th>
               <th align=left>Teléfono : </th><th align=left>{{ $invoice['telefono'] }}</th></tr>
           <tr><th align=left>Ciudad : </th><th align=left>{{ $invoice['ciudad'] }}</th>
               </tr>  
      </table>
      <div id="details" class="clearfix">
      </div>
      <table width=90% border="1" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="no">Cantidad</th>
            <th class="desc">Descripcion</th>
            <th class="unit">Valor</th>
            <th class="total">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $d): ?>
          <tr style="page-break-after: always;">
            <td class="no">{{ $d['cantidad'] }}</td>
            <td class="desc">{{ $d['servicio'] }}
               @if ($d['tiempo']) 
                  - {{ $d['tiempo'] }}
               @endif 
            </td>
            <td class="unit">{{ number_format($d['valor']) }}</td>
            <td class="total">{{ number_format($d['valor']*$d['cantidad']) }} </td>
          </tr>
          <?php endforeach ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"></td>
            <td >TOTAL</td>
            <td>{{ number_format($invoice['valor']) }}</td>
          </tr>
        </tfoot>
      </table>
       <table width=80%>
           <tr><th align=left>Entrega : </th><th align=center>________________________________</th>
               <th align=left>Recibe : </th> <th align=center>________________________________</th>
       </tr></table>
     <!--  <div class='footer'>
        <small>
              MEDELLÍN: Cra. 76 No. 43-24 - PBX: (4) 430 20 20    |     
              PEREIRA: Cra. 11 No. 50-57 - PBX: (6) 326 47 02     |  
              CALI: Carrera 6 No. 20-66 - PBX: (2) 5241801        
        </small>
      </div> -->
  </body>
</html>