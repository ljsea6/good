<?php
namespace App\Http\Controllers;
use App\Entities\Ciudad;
use App\Entities\Oficina;
use App\Entities\Tercero;
use App\Entities\Tipo;
use App\Entities\Network;
use App\Http\Controllers\Controller;
use App\Http\Requests\Redes\Nuevared;
use App\Http\Requests\Redes\EditaRed;
//use App\Http\Requests\Terceros\Usuarios\EditaUsuario;
//use App\Http\Requests\Terceros\Usuarios\NuevoUsuario;
//use App\Http\Requests\Terceros\Usuarios\NuevoUsuarioexterno;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;
use Validator;
class NetworksController extends Controller {
    /**
     * Display a listing of the  resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.networks.index');
    }
    public function anyData() {
        $networks = Network::select('id', 'name', 'frontal_amount', 'depth')->orderby('id')->get();
        $results = collect($networks);
        return Datatables::of($results)
            ->addColumn('action', function ($results) {
                return '<div align=center><a href="' . route('admin.networks.edit', $results->id) . '"  class="btn btn-warning btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>';
            })
            ->addColumn('id', function ($results) {
                return '<div align=left>' . $results->id . '</div>';
            })
            ->addColumn('name', function ($results) {
                return '<div align=left>' . $results->name . '</div>';
            })
            ->addColumn('frontal_amount', function ($results) {
                return '<div align=left>' . $results->frontal_amount . '</div>';
            })
            ->addColumn('depth', function ($results) {
                return '<div align=left>' . $results->depth . '</div>';
            })
        ->make(true);
    }
    public function hijos($id) {
        $usuarios = Tercero::tipoUsuario(2)->with('ciudad')->orderby('id')->paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.networks.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:networks,name|string',
            'frontal_amount' => 'required|int',
            'depth' => 'required|int',
        ]);
        if ($validator->fails()) {
            return redirect()
                ->route('admin.networks.create')
                ->withErrors($validator)
                ->withInput();
        }
        $result = Network::create([
            'name' => $request['name'],
            'frontal_amount' => $request['frontal_amount'],
            'depth' => $request['depth'],
        ]);
        if($result) {
            return redirect()
                ->route('admin.networks.index')
                ->with([
                    'status' => 'Se ha agregado la red: ' . strtoupper($result->name)
                ]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $network = Network::findOrFail($id);
        return view('admin.networks.edit',compact('network'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:networks,name|string',
            'frontal_amount' => 'required|int',
            'depth' => 'required|int',
        ]);
        if ($validator->fails()) {
            return redirect()
                ->route('admin.networks.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
        $network = Network::findOrFail($id);
        $name = $network->name;
        $network->name = $request['name'];
        $network->frontal_amount = $request['frontal_amount'];
        $network->depth = $request['depth'];
        $result = $network->save();
        if($result) {
            return redirect()
                ->route('admin.networks.index')
                ->with([
                    'status' => 'Se ha actualizado la red: ' . strtoupper($name) . ' a ' . strtoupper($network->name)
                ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
    protected function getusuario()
    {
       // $ciudades   = Ciudad::get()->lists('nombre_completo', 'id');
       $tipos= Tipo::get()->lists('nombre','id');//->toarray();
        //$oficinas =  Oficina::lists('nombre', 'id');
       // $roles      = Role::lists('name', 'id');
        //$clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
     return view('admin.usuarios.createusua', compact('tipos'));
    }
    public function storeNuevo(NuevoUsuarioexterno $request)
    {
    //ChangedProperties::select('changed_property','change_type','previous_value','updated_value')->get();
          $query=(Tercero::select ('id')->where('email','=',($request->email_Patrocinador))->get());
    //Tercero::select('terceros.id')->where('email','=',($request->email_Patrocinador));
            $usuario = new Tercero($request->all());
    /*
            if ($avatar) {
                $usuario->avatar = "uploads/avatar/" . $avatar_nombre;
            }
    */      //$usuario->identificacion=0;
             $usuario->contraseña = bcrypt($request->contraseña);
            $usuario->contraseña = bcrypt($request->contraseña);
            $usuario->tipo_id     =$request->tipo_id;
            $usuario->usuario=$request->email;
              $usuario->padre_id =$query[0]->id;
            //$usuario->rol_id
            //if ($request->cliente
          //  }
             //Toco pasarla manual, por que el request no la actualizaba.
           $usuario->usuario_id =  1;
          // $usuario->ip         = 54;
            //$usuario->tipo_id    = $request->tipo_id;
            $usuario->save();
           //$permisos = Role::findOrFail('1')->permissions;
           // foreach ($permisos as $per) {
             //   dd($per->id);
              //$permiso = Permission::findOrFail($per->id);
               // $usuario = Tercero::findOrFail($usuario->id);
                //$usuario->attachPermission($permiso);
            //}
            //dd($usuario->id);
            Alert::message("! Usuario registrado con éxito !  ", 'success');
            return view('admin.payu.payu',compact('usuario', 'tipos', 'usuario_id'));
        }
    }