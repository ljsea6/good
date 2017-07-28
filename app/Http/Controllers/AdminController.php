<?php
namespace App\Http\Controllers;
use App\Entities\Network;
use App\Entities\Tercero;
use App\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Mail;
class AdminController extends Controller {
    
    public function email()
    {
        return view('admin.send.mail');
    }
    
    public function send(Request $request)
    {
        $data = $request->all();
        Mail::send('admin.send.message', $data, function($message) use ($request) {
           $message->subject($request->body);
           $message->to($request->email);
        });
        
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
        $results = Tercero::where('email', 'like', '%' .strtolower($request['email']) . '%')->get();
        
        return view('admin.find', compact('results'));
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