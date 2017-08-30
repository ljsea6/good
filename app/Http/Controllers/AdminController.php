<?php
namespace App\Http\Controllers;
use App\Entities\Network;
use App\Entities\Tercero;
use App\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Yajra\Datatables\Datatables;
use Mail;
use DB;

class AdminController extends Controller {
    
    public function email()
    {
        return view('admin.send.mail');
    }
    
    public function send(Request $request)
    {
        $data = $request->all();
       
        if(isset($data['email']) && $data['email'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->email);
            });
        }
        
        if(isset($data['emailone']) && $data['emailone'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->emailone);
            });
        }
        
        if(isset($data['emailtwo']) && $data['emailtwo'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->emailtwo);
            });
        }
        
        if(isset($data['email1']) && $data['email1'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->email1);
            });
        }
        
        if(isset($data['email2']) && $data['email2'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->email2);
            });
        }
        
        if(isset($data['email3']) && $data['email3'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->email3);
            });
        }
        
        if(isset($data['email4']) && $data['email4'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->email4);
            });
        }
        
         if(isset($data['email5']) && $data['email5'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->email2);
            });
        }
        
        if(isset($data['email6']) && $data['email6'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->email3);
            });
        }
        
        if(isset($data['email7']) && $data['email7'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->subject($request->body);
                $message->to($request->email4);
            });
        }
        
        return view('admin.send.success');
    }

    public function buscar(Request $request)
    {
        $tercero = Tercero::where('email', strtolower($request['email']))->get();
        return view('admin.search.index', compact('tercero'));
    }

    public function search()
    {
        return view('admin.search');
    }

    public function finder(Request $request)
    {
        if(currentUser()->tipo_id == 2) {
            $results = Tercero::where('email', 'like', '%' .strtolower($request['email']) . '%')->get();

            return view('admin.find', compact('results'));
        }

        if (currentUser()->tipo_id != 2 && currentUser()->tipo_id != 1) {

            $results = Tercero::where('email', '=', $request['email'])->get();

            return view('admin.find', compact('results'));
        }

        if (currentUser()->tipo_id == 1) {

            $results  = Tercero::where('email', '=', $request['email'])->first();

            if (count($results) > 0) {
                $find = DB::table('terceros_networks')->where('customer_id', $results['id'])->first();

                if ($find->padre_id == currentUser()->id) {
                    return view('admin.find', compact('results'));
                } else {
                    $err = 'No está en su lista de referidos';
                    return view('admin.find', compact('err'));
                }
            } else {
                $err = 'No está en su lista de referidos';
                return view('admin.find', compact('err'));
            }




        }
        
    }

    public function network()
    {
        $tercero = Tercero::find(currentUser()->id);
        $referidos  = DB::table('terceros')
            ->where('apellidos',  $tercero['email'])
            ->select('id', 'nombres', 'email')
            ->get();

        $orders = Order::where('email', $tercero['email'])->get();
        $total = 0;

        foreach ($referidos as $referido) {
            $results = Order::where('email', $referido->email)->get();
            if (count($results) > 0) {
                foreach ($results as $result) {
                    $total = $total + (double)$result['total_price'];
                }
            }
        }

        $totalPrice = number_format($total, 0);
        $networks = Network::all();
        $terceros = [];

        foreach ($networks as $network) {
            $results = DB::table('terceros')
                ->join('networks', 'terceros.network_id', '=', 'networks.id')
                ->where('terceros.apellidos',  $tercero['email'])
                ->where('networks.id', $network['id'])
                ->select('terceros.id', 'terceros.nombres', 'terceros.apellidos','terceros.email', 'terceros.network_id')
                ->take(10)->get();
            foreach ($results as $result) {
                array_push($terceros, (array)$result);
            }
        }

        $send = [
            'referidos' => number_format(count($referidos)),
            'orders'  => number_format(count($orders)),
            'total' => $totalPrice,
            'terceros' => collect($terceros),
            'tercero' => $tercero,
            'redes' => $networks
        ];

        return view('admin.network', compact('send'));
    }

    public function index()
    {
        $send = Tercero::find(currentUser()->id);
        return view('admin.index', compact('send'));
    }

    public function anyData(Request $request)
    {
        $tercero = Tercero::find((int)$request['id']);
        $results  = DB::table('terceros')
            ->join('networks', 'terceros.network_id', '=', 'networks.id')
            ->where('terceros.apellidos',  $tercero['email'])
            ->select('terceros.id', 'terceros.nombres', 'terceros.email', 'networks.name')
            ->get();
        $send = collect($results);
        return Datatables::of($send)
            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send->id . '</div>';
            })
            ->addColumn('nombres', function ($send) {
                return '<div align=left>' . $send->nombres . '</div>';
            })
            ->addColumn('email', function ($send) {
                return '<div align=left>' . $send->email . '</div>';
            })
            ->addColumn('name', function ($send) {
                return '<div align=left>' . $send->name . '</div>';
            })
            ->make(true);
    }

    public function indexproveedores()
    {
        return view('admin.proveedores.index');
	}
}