<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Archivo;
use App\Entities\Estado;
use App\Entities\Orden;
use App\Entities\Envio;
use App\Entities\Tercero;
use DB;
use Mail;
use Carbon\Carbon;
use Excel;

class SubirBaseEnvios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subirbd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subir bases de datos pendientes por procesar';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //1. Leemos la tabla de archivos, para revisar cuales estan pendientes de subir.
        $archivos        = Archivo::select('nombre','id','usuario_id')->where('tipo_id', 27)->where('estado',1)->get();

        //2. Recorremos los archivos y creamos la tabla temporal en base al archivo que subimos.
        $b=0;
        foreach ($archivos as $archivo) {
            $id = $archivo->id;
  
            $tercero = Tercero::select('nombres','apellidos','email','rol_id')->find($archivo->usuario_id);
            //dd($tercero->email);

            //Actualizamos el estado del archivo para marcarlo como procesando.
            //Estados: 
            //  1. Subido
            //  2. En proceso.
            //  3. Procesado.

            $archivo_up = Archivo::find($id);
            //$archivo_up->estado = 2;
            $archivo_up->save();

            $GLOBALS["men"]="";

            if ($tercero->rol_id==11) {  //Si el que subio el archivo es un courier, procesamos los estados      

                $GLOBALS["men"]="Se proceso el archivo ".$archivo->nombre." para actualizar los estados de los envios, este fue el resultado : \r\n \r\n";
                Excel::filter('chunk')->load('public\uploads\recived_files\files'.'\\'.$archivo->nombre)->chunk(250, function($results) {
                    foreach ($results as $row) {
                        $estado_id = Estado::where('nombre',$row->estado)->first();
                        if ($estado_id) {
                            
                            $envio = Envio::where('idenvio',$row->idenvio)->first();
                            $envio->estado_id = 1;
                            $envio->save();

                            $GLOBALS["men"].="Envio ".$row->idenvio." actualizado\r\n";
                        } else  {
                            $GLOBALS["men"].="Envio ".$row->idenvio." no existe\r\n";
                        } 
                    }
                });
            } else { //Si el que subio el archivo es otro perfil, es una base de una orden, la procesamos.

                $procesar_uno_uno=0;
                if ($procesar_uno_uno==1) {
                    //Excel::filter('chunk')->load('public\uploads\recived_files\files'.'\\'.$archivo->nombre)->chunk(250, function($results) {
                    Excel::filter('chunk')->load('public/uploads/recived_files/files'.'//'.$archivo->nombre)->chunk(250, function($results) {
                      foreach ($results as $row) {

                        if ($row<>'') {
                            print($row)."\r\n";
                            print($row->idenvio)."\r\n";

                            $sql="select fsubir_envio(".$row->idenvio.",".$row->cuenta.")";
                            dd($sql);
                            //$result = DB::select($sql);
                            
                            $GLOBALS["men"].="Orden procesada sin novedades.";

                            $GLOBALS["men"].="Envio  actualizado\r\n";
                        }
                        
                      }
                    });
                } else {
                    $nombre = "C:\\".$archivo->nombre;
                    $nombre = base_path()."/public/uploads/recived_files/files/".$archivo->nombre;
                    $numero_orden = substr($archivo->nombre,0,-4);

                    $orden = Orden::select('id','responsable_id')->where('numero', $numero_orden)->first();

                    if (empty($orden))  {
                        $GLOBALS["men"].="Orden ".$numero_orden." no existe";
                    } else {
                        $idorden = $orden->id;
                        
                        $GLOBALS["men"]="Se proceso el archivo ".$nombre." para la orden ".$numero_orden.", este fue el resultado : ";

                        if ($idorden) {
                            $orden->subida_base = Carbon::now();
                            $orden->save();

                            //3. Insertamos en la tabla definitiva, normalizando la direccion, indexando el destinatario y asignando zonas.
                            $sql="select fsubir_bd('$nombre',$idorden,$id)";
                            //var_dump($sql);
                            $result = DB::select($sql);
                            
                            $GLOBALS["men"].="Orden ".$idorden." procesada sin novedades.";                            
                        }
                    }
                }
            }

            Mail::raw($GLOBALS["men"], function($message)
                    {
                        $message->from('oscarfonseca@vincom.co','Informes Domina');
                        //$message->to($tercero->email,$tercero->nombres.' '.$tercero->apellidos)->subject('Orden procesada');
                        $message->to('oscarfonseca@vincom.co','Oscar Fonseca')->subject('Orden procesada');
            });

            $b++;
        }

        $this->info($b.' Bases procesadas');
    }


}
