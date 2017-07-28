var express = require('express');
var app = express();
var pg = require("pg")
pg.defaults.poolSize = 50;

// Update a record 
var funcion_bd = function(req, res, proc) {
    var callback=req.param('callback');
    var id=req.param('id');
    var mn=req.param('mn');
    var idt=req.param('idt');
    var fo=req.param('fo');
    var fecha=req.param('fecha');
    var inc=req.param('inc');
    var vcaja=req.param('caja');
    var resp= { r : "" };
    
    // Connect to DB
    //var conString = "pg://postgres:p20@localhost:5432/domina_faseII";
    var conString = "pg://node:113o9WC*%t2j@localhost:5432/domina";
    var client = new pg.Client(conString);

    // connect to our database 
    client.connect(function (err) {
      if (err) throw err;

      //Si el proceso es 1, es pistoleo de manifiestos.
      if (proc==1) {
        var sql='select fmanifiestos('+id+','+mn+','+idt+','+fo+') as ret';
      } else if (proc==2) {
        if (fecha=='null') fecha='now()';
        else fecha="'"+fecha+"'";

        if (vcaja=='null') vcaja='null';
        else vcaja="'"+vcaja+"'";

        //Si el proceso es 2, es punteo de devoluciones y entregas.
        var sql="select fpunteo("+id+","+mn+","+idt+","+fecha+","+inc+","+vcaja+") as ret";
        //console.log(sql);        
      } else if (proc==3) {
        //Si el proceso es 3, es firmado digital.
        var sql='select ffirmar_salida_zona('+id+','+idt+') as ret';
      } else if (proc==4) {
        //Si el proceso es 4, es liberacion de envios.
        var sql='select fliberar_envio('+id+','+idt+') as ret';
      }      
      console.log(sql);
      client.query(sql, function (err, result) {
        if (err) throw err;
     
        // just print the result to the console 
        resp.r=result.rows[0].ret;

        // disconnect the client 
        client.end(function (err) {
          if (err) throw err;
        });

        res.writeHead(200, {"Content-Type": "application/json"});
        res.write(callback+'('+JSON.stringify(resp)+')');
        res.end();
      });
    });
}

app.all('/', function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "X-Requested-With");
  next();
 });

app.get('/pistoleo_zona', function (req, res) {
  //var body = req.params;
  funcion_bd(req,res,1);
});

app.get('/punteo', function (req, res) {
  //var body = req.params;
  funcion_bd(req,res,2);
});

app.get('/firmar', function (req, res) {
  //var body = req.params;
  funcion_bd(req,res,3);
});

app.get('/liberar', function (req, res) {
  //var body = req.params;
  funcion_bd(req,res,4);
});

app.listen(3000, function () {
  console.log('Aplicacion de pistoleo escuchando por el puerto 3000 !');
});
