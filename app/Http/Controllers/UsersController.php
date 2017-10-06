<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Dingo\Api\Routing\Helpers;
use App\Transformers\UserTransformer;
use App\Transformers\TerceroTransformer;
use Authorizer;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use GuzzleHttp\Client;
use App\Entities\Tercero;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\Entities\Ciudad;
use App\Entities\Oficina;
use App\Entities\Tipo;
use App\Entities\Network;
use App\Entities\Tercero_network;
use App\Http\Requests\Terceros\Usuarios\EditaUsuario;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuario;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuarioexterno;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Queue\RedisQueue;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;
use DB;
use View;

class UsersController extends Controller
{
    use Helpers, AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $username = 'usuario';

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials', true);
        header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
        header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    }

    public function verify_code(Request $request)
    {

        if (isset($request['code']) && isset($request['email'])) {

            $code = $request['code'];
            $email = $request['email'];

            if ($code != $email) {

                $result = Tercero::where('email', strtolower($code))->first();

                if (count($result) > 0) {


                    return $this->response->array(['message' => 'El código es valido']);

                } else {

                    return $this->response->array(['error' => 'El código no es valido']);
                }

            } else {
                return $this->response->array(['error' => 'El código no es valido']);
            }

        } else {
            return $this->response->array(['error' => 'El código no es valido']);
        }
    }
    public function authorizeGet()
    {
        $authParams = Authorizer::getAuthCodeRequestParams();
        $formParams = array_except($authParams,'client');
        $formParams['client_id'] = $authParams['client']->getId();
        $formParams['scope'] = implode(config('oauth2.scope_delimiter'), array_map(function ($scope) {
            return $scope->getId();
        }, $authParams['scopes']));
        return View::make('api.authorization-form', ['params' => $formParams, 'client' => $authParams['client']]);
    }
    public function authorizePost(Request $request)
    {

        $params = Authorizer::getAuthCodeRequestParams();
        $params['user_id'] = Auth::user()->id;
        $redirectUri = '/';
        // If the user has allowed the client to access its data, redirect back to the client with an auth code.
        if ($request->has('approve')) {
            $redirectUri = Authorizer::issueAuthCode('user', $params['user_id'], $params);
        }
        // If the user has denied the client to access its data, redirect back to the client with an error message.
        if ($request->has('deny')) {
            $redirectUri = Authorizer::authCodeRequestDeniedRedirectUri();
        }
        return redirect($redirectUri);

    }
    public function access(Request $request)
    {
        $client = new Client();
        try {
            $res = $client->request('post', 'http://localhost/api/oauth/access_token', [
                'json' => [
                    "grant_type" => "password",
                    "client_id" => $request['client_id'],
                    "client_secret" => $request['client_secret'],
                    "username" => $request['username'],
                    "password" => $request['password']
                ]
            ]);
        } catch (RequestException $e) {
            return redirect()->back()->with([
                'error' => 'Usuario o contraseña incorrecto, por favor verifique sus datos.'
            ]);
        }
        $code = $res->getStatusCode();
        $results = json_decode($res->getBody(), true);
        if ($code == 200) {
            $access_token = $results['access_token'];
            $token_type = $results['token_type'];
            $expires_in = $results['expires_in'];
            $request = $client->request('get', 'http://localhost/api/oauth/access/login', [
                'headers' => [
                    "Authorization" => $token_type . " " . $access_token,
                ]
            ]);
            $code_final = $request->getStatusCode();
            if ($code_final == 200) {
                return 'sirve';
            }
        }
    }
    public function login()
    {
        return view('api.index');
    }
    public function verify($username, $password)
    {
        $credentials = [
            'email'    => $username,
            'password' => $password,
        ];
        if (Auth::once($credentials)) {
            return Auth::user()->id;
        }
        return false;
    }
    public function authorization()
    {
        return $this->response->array(Authorizer::issueAccessToken());
    }
    public function index()
    {
        $permisos = Permission::lists('name', 'id');
        return view('admin.usuarios.index', compact('permisos'));
    }
    public function anyData()
    {
        $usuarios = Tercero::select('terceros.id', 'terceros.avatar', 'terceros.identificacion', 'terceros.nombres', 'terceros.apellidos', 'terceros.direccion', 'ciudades.nombre as ciudad', 'terceros.email', 'roles.name as rol', 'tipos.nombre as tipo')
            ->leftjoin('ciudades', 'terceros.ciudad_id', '=', 'ciudades.id')
            ->leftjoin('roles', 'terceros.rol_id', '=', 'roles.id')
            ->leftjoin('tipos','tipos.id','=','terceros.tipo_id')
            ->orderby('terceros.id');
        return Datatables::of($usuarios)
            ->addColumn('identificacion', function ($usuarios) {
                return '<div align=left>'.$usuarios->identificacion.'</div>';
            })
            ->addColumn('nombres', function ($usuarios) {
                return '<div align=left>'.$usuarios->nombres.'</div>';
            })
            ->addColumn('apellidos', function ($usuarios) {
                return '<div align=left>'.$usuarios->apellidos.'</div>';
            })
            ->addColumn('direccion', function ($usuarios) {
                return '<div align=left>'.$usuarios->direccion.'</div>';
            })
            ->addColumn('ciudad', function ($usuarios) {
                return '<div align=left>'.$usuarios->ciudad.'</div>';
            })
            ->addColumn('rol', function ($usuarios) {
                return '<div align=left>'.$usuarios->rol.'</div>';
            })
            ->addColumn('tipo', function ($usuarios) {
                return '<div align=left>'.$usuarios->tipo.'</div>';
            })
            ->addColumn('permisos', function ($usuarios) {
                return '
                    <div align=left>
                        <a data-toggle="modal" tercero_id="' . $usuarios->id . '" data-target="#permisos" class="btn btn-primary btn-xs get-permisos" OnClick="get_permisos(' . $usuarios->id . ');">Permisos</a>
                    </div>
                ';
            })
            ->addColumn('action', function ($usuarios) {
                return '
                    <div align=left>
                        <a  href="' . route('admin.users.edit', $usuarios->id) . '"  class="btn btn-warning btn-xs">
                            <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                        </a>
                        <a href="' . route('admin.users.destroy', $usuarios->id) . '"  onclick="return confirm(\'¿ Desea eliminar el registro seleccionado ?\')" class="btn btn-danger btn-xs">
                                <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                        </a>
                    </div>
                ';
            })
            ->editColumn('avatar', function ($usuarios) {
                if ($usuarios->avatar !== NULL) {
                    return '<div align=left><img src="' . asset($usuarios->avatar) . '" class="img-circle avatar_table" /></div> ';
                } else {
                    return '<div align=left><img src="' . asset('img/avatar-bg.png') . '" class="img-circle avatar_table"/></div>';
                }
            })
            ->make(true);
    }
    public function hijos($id)
    {
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
        $ciudades   = Ciudad::get()->lists('nombre_completo', 'id');
        $tipos = Tipo::get()->lists('nombre','id');//->toarray();
        $oficinas =  Oficina::lists('nombre', 'id');
        $roles      = Role::lists('name', 'id');
        $clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
        $red = Network::lists('name','id');
        return view('admin.usuarios.create', compact('ciudades','tipos', 'oficinas', 'roles', 'clientes','red'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevoUsuario $request)
    {
        $avatar = $request->file('avatar');
        $query = Tercero::select('id')->where('email', '=', ($request->email_Patrocinador))->first();
        if ($avatar) {
            $avatar_nombre = str_random(30) . "." . $avatar->getClientOriginalExtension();
            $path = public_path() . "/uploads/avatar/";
            $avatar->move($path, $avatar_nombre);
        }
        $usuario = new Tercero($request->all());
        $usuario->save();
        if ($avatar) {
            $usuario->avatar = "uploads/avatar/" . $avatar_nombre;
        }
        $usuario->contraseña = bcrypt($request->contraseña);
        $usuario->tipo_id = $request->tipo_id;
        if ($request->cliente) {
            $usuario->padre_id = $request->cliente;
        }
        $usuario->rol_id = $request->rol_id;
        $usuario->usuario_id = currentUser()->id;
        $padre = Tercero::find($query->id);
        $padre->numero_referidos = $padre->numero_referidos + 1;
        $usuario->ip = $request->ip();
        $usuario->networks()->attach(1, ['padre_id' => $padre->id]);
        $padre->save();
        $usuario->save();
        $permisos = Role::findOrFail($request->rol_id)->permissions;
        foreach ($permisos as $per) {
            $permiso = Permission::findOrFail($per->id);
            $usuario = Tercero::findOrFail($usuario->id);
            $usuario->attachPermission($permiso);
        }
        Alert::message("! Usuario registrado con éxito !  ", 'success');
        return redirect()->route('admin.usuarios.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $usuario    = Tercero::findOrFail($id);
        $ciudades   = Ciudad::get()->lists('nombre_completo', 'id');
        $tipos = Tipo::get()->lists('nombre','id');//TipoIdenti(tipo_id)->
        $oficinas = Oficina::lists('nombre', 'id');
        $roles      = Role::lists('name', 'id');
        $clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
        return view('admin.usuarios.edit', compact('usuario', 'tipos','ciudades', 'oficinas', 'roles', 'clientes'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditaUsuario $request, $id) {
        //dd("- ".($request->telefono)." -");
        $usuario = Tercero::findOrFail($id);
        $usuario->fill($request->all());
        if ($request->file("avatar")) {
            $avatar        = $request->file('avatar');
            $avatar_nombre = str_random(30) . "." . $avatar->getClientOriginalExtension();
            $path          = public_path() . "/uploads/avatar/";
            $avatar->move($path, $avatar_nombre);
            $usuario->avatar = "uploads/avatar/" . $avatar_nombre;
        }
        //$usuario->contraseña = bcrypt($request->contraseña);
        $usuario->rol_id      = $request->rol_id; //Toco pasarla manual, por que el request no la actualizaba.
        $usuario->usuario_id  = currentUser()->id;
        $usuario->ip          = $request->ip();
        $usuario->tipo_id    = $request->tipo_id;
        //Tipo::TipoIdenti($request->tipo_id)->get();
        $usuario->save();
        Alert::message('! Usuario ' . $usuario->nombres . " " . $usuario->apellidos . " Actualizado con éxito ! ", 'success');
        return redirect()->route('admin.usuarios.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $usuario = Tercero::findOrFail($id);
        $usuario->delete();
        Alert::message('! Usuario ' . $usuario->nombres . " " . $usuario->apellidos . " eliminado con éxito! ", 'success');
        return redirect()->route('admin.usuarios.index');
    }
    protected function getusuario() {
        // $ciudades   = Ciudad::get()->lists('nombre_completo', 'id');
        $tipos= Tipo::get()->lists('nombre','id');//->toarray();
        //$oficinas =  Oficina::lists('nombre', 'id');
        // $roles      = Role::lists('name', 'id');
        //$clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
        return view('admin.usuarios.createusua', compact('tipos'));
    }
    public function storeNuevo(NuevoUsuarioexterno $request) {
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