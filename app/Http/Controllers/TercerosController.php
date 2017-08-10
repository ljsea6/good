<?php
namespace App\Http\Controllers;

use App\Entities\Network;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Entities\Tercero;
use App\Order;
use Yajra\Datatables\Datatables;
use DB;
use Carbon\Carbon;

class TercerosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.terceros.index');
    }

    public function anyData()
    {
      
        $referidos = Tercero::select('id', 'identificacion', 'nombres', 'apellidos', 'email', 'numero_referidos', 'numero_ordenes_referidos', 'total_price_orders')
                ->where('state', true)
                ->get();
        
        $send = collect($referidos);

        return Datatables::of($send )
            ->addColumn('action', function ($send ) {
                return '<div align=center><a href="' . route('admin.terceros.show', $send['id']) . '"  class="btn btn-success btn-xs">
                        Red
                </a></div>';
            })
            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send['id'] . '</div>';
            })
            ->addColumn('identificacion', function ($send) {
                return '<div align=left>' . $send['identificacion'] . '</div>';
            })
            ->addColumn('nombres', function ($send) {
                return '<div align=left>' . $send['nombres'] . '</div>';
            })
            ->addColumn('apellidos', function ($send) {
                return '<div align=left>' . $send['apellidos'] . '</div>';
            })
            ->addColumn('email', function ($send) {
                return '<div align=left>' . $send['email'] . '</div>';
            })
            ->addColumn('numero_referidos', function ($send) {
                return '<div align=left>' . number_format($send['numero_referidos']) . '</div>';
            })
            ->addColumn('numero_ordenes_referidos', function ($send) {
                return '<div align=left>' . number_format($send['numero_ordenes_referidos']) . '</div>';
            })
            ->addColumn('total_price_orders', function ($send) {
                return '<div align=left>' . number_format($send['total_price_orders']) . '</div>';
            })
             ->addColumn('edit', function ($send) {
                return '<div align=center><a href="' . route('admin.terceros.edit', $send['id']) . '"  class="btn btn-warning btn-xs">
                        Editar
                </a></div>';
            })
            ->make(true);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $referidos = array();
        
        if ($id == 26) {
            $tercero = Tercero::with('networks')->find($id);
            $networks = $tercero->networks;
            
                 $results = DB::table('terceros')
                    ->select('terceros_networks.customer_id')
                    ->join('terceros_networks', 'terceros.id', '=', 'terceros_networks.padre_id')
                    ->where('terceros.id', $id)  
                     ->where('terceros_networks.network_id', 1)
                    ->get();
               
            foreach ($results as $result) {
                $ter = Tercero::select('id', 'nombres', 'apellidos', 'email')->find($result->customer_id);
                array_push($referidos, $ter);
            }   
           
            
            $send = [
                'networks' => $networks,
                'referidos' => $referidos
            ];
            
            
            return view('admin.terceros.show', compact('send')); 
        } else {
            $tercero = Tercero::with('networks')->find($id);
            $networks = $tercero->networks;
            foreach ($networks as $network) {
                $results = Tercero::select('id', 'nombres', 'apellidos', 'email')
                ->where('apellidos', strtolower($tercero['email']))
                ->where('state', true)
                ->where('network_id', $network['id'])
                ->get();
                array_push($referidos, $results);
            }
            
            $send = [
                'networks' => $networks,
                'referidos' => $referidos[0]
            ];
            return view('admin.terceros.show', compact('send')); 
        
        }
    }

    public function edit($id)
    {
        $tercero = Tercero::find($id);
        
        if($tercero->state === true) {
           return view('admin.terceros.edit', compact('tercero')); 
        }

        
    }

    public function update(Request $request, $id) 
    {
        $state = $request['state'];
        

        if ($state === 'false') {

            $tercero = Tercero::with('networks')->find($id);
            $tercero->state = $state;
            $tercero->save();
            
            $networks = $tercero['networks'];
            $father = $networks[0]['pivot']['padre_id'];


            if(!is_null($father)){

                $referidos = DB::table('terceros_networks')->where('padre_id', $tercero->id)->get();
             
                if(count($referidos) > 0) {

                    foreach ($referidos as $referido) {
                        DB::table('terceros_networks')->where('customer_id', $referido->customer_id)->update(['padre_id' => $father]);
                        DB::insert('insert into referidos_logs (tercero_id, old_father, new_father, created_at, updated_at) values (?, ?, ?, ?, ?)', [
                           $referido->customer_id, 
                           $referido->padre_id,
                           $father,
                           Carbon::now(),
                           Carbon::now(), 
                        ]);
                    }
                }
            }

            if(is_null($father)) {

                $referidos = DB::table('terceros_networks')->where('padre_id', $tercero->id)->get();
                
                if(count($referidos) > 0) {
                    
                    foreach ($referidos as $referido) {
                       DB::table('terceros_networks')->where('customer_id', $referido->customer_id)->update(['padre_id' => null]);
                       DB::insert('insert into referidos_logs (tercero_id, old_father, new_father, created_at, updated_at) values (?, ?, ?, ?, ?)', [
                           $referido->customer_id, 
                           $referido->padre_id,
                           null,
                           Carbon::now(),
                           Carbon::now(), 
                        ]);
                    }                    
                }

            }
            
            

            
            DB::table('terceros_logs')->insert([
                'tercero_id' => $tercero->id, 
                'padre_id' => $father,
                'user' => currentUser()->nombre_completo,
                'ip' => $request->ip(),
                'browser' => $request->server('HTTP_USER_AGENT'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
                ]);
            
            return redirect('admin/terceros')->with(['status' => 'Se han hechos los cambios correctamente']);
            
        } else {
            
            return redirect('admin/terceros')->with(['status' => 'No se han hecho cambios.']);
        }    
    }
    
}
