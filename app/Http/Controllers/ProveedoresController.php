<?php
namespace App\Http\Controllers;

use App\Entities\Ciudad;
use App\Entities\Oficina;
use App\Entities\Tercero;
use App\Entities\Tipo;
use App\Entities\Archivo;
use App\Entities\Network;
use App\Http\Controllers\Controller;
use App\Http\Requests\Terceros\Usuarios\EditaUsuario;
use App\Http\Requests\Terceros\Proveedor\EditaUsuarioProveedor;
use App\Http\Requests\Terceros\Proveedor\NuevoUsuarioproveedor;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuario;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuarioexterno;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;

class ProveedoresController extends Controller {

    /**
     * Display a listing of the  resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $permisos = Permission::lists('name', 'id');

//$usuarios = Tercero::with('ciudad')->tipoUsuario(2)->orderby('id')->paginate(10);
        //dd($usuarios);
        return view('admin.proveedores.index', compact('permisos'));
    }

    public function anyData() {


        $usuarios = Tercero::select('terceros.id', 'terceros.avatar', 'terceros.identificacion', 'terceros.nombres', 'terceros.apellidos', 'terceros.direccion', 'c.nombre as ciudad', 'terceros.email', 'r.name as rol', 'terceros.nombres as empresa','i.nombre')->leftjoin('ciudades as c', 'terceros.ciudad_id', '=', 'c.id')->leftjoin('roles as r', 'terceros.rol_id', '=', 'r.id')->leftjoin('tipos as i','i.id','=','terceros.tipo_id') ->where('i.nombre','=','Proveedor')->orderby('id');


//$usuarios = Tercero::select('terceros.id', 'terceros.avatar', 'terceros.identificacion', 'terceros.nombres', 'terceros.apellidos', 'terceros.direccion', 'c.nombre as ciudad', 'terceros.email', 'r.name as rol', 'e.nombres as empresa','i.nombre')->leftjoin('ciudades as c', 'terceros.ciudad_id', '=', 'c.id')->leftjoin('roles as r', 'terceros.rol_id', '=', 'r.id')->leftjoin('terceros as e', 'terceros.padre_id', '=', 'e.id')->leftjoin('tipos as i','i.id','=','e.tipo_id')->orderby('id');

        return Datatables::of($usuarios)
       

            ->addColumn('permisos', function ($usuarios) {
                return '
                <a data-toggle="modal" tercero_id="' . $usuarios->id . '" data-target="#permisos" class="btn btn-primary btn-xs get-permisos" 
                OnClick="get_permisos(' . $usuarios->id . ');">
                                Permisos
                </a>';
            })
            ->addColumn('action', function ($usuarios) {
                return '
                <a href="' . route('admin.proveedores.edit', $usuarios->id) . '"  class="btn btn-warning btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.proveedores.destroy', $usuarios->id) . '"  onclick="return confirm(\'¿ Desea eliminar el registro seleccionado ?\')" 
                class="btn btn-danger btn-xs">
                        <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                </a>';
            })
            ->editColumn('avatar', function ($usuarios) {

                if ($usuarios->avatar !== NULL) {
                    return '<img src="' . asset($usuarios->avatar) . '" class="img-circle avatar_table" />';
                } else {
                    return '<img src="' . asset('img/avatar-bg.png') . '" class="img-circle avatar_table"/>';
                }

            })
            ->make(true);
    }

    public function hijos($id) {
        $usuarios = Tercero::tipoUsuario(2)->with('ciudad')->orderby('id')->paginate(10);

        return view('admin.proveedores.index', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {



        $ciudades   = Ciudad::get()->lists('nombre_completo', 'id');
       $tipos = Tipo::tipoUsuario()->get()->lists('nombre','id');//->where('nombre','=','Proveedor');

       //Tipo::get()->lists('nombre','id')->where('nombre','=','"Proveedor"');//->toarray();"Proveedor"
        $oficinas =  Oficina::lists('nombre', 'id');
        $roles      = Role::lists('name', 'id');
        $clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
        
//$tipos=tipo::select('id','nombre','padre_id')->leftjoin('terceros as a', 'a.tipo_id','=','tipos.id');
  //      return datatables::of($tipos)
        return view('admin.proveedores.create', compact('ciudades','tipos', 'oficinas', 'roles', 'clientes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevoUsuarioproveedor $request) {

        $query=Tipo::select('id')->where('nombre','=','Proveedor');
        $avatar = $request->file('avatar');

        if ($avatar) {
            $avatar_nombre = str_random(30) . "." . $avatar->getClientOriginalExtension();
            $path          = public_path() . "/uploads/avatar/";
            $avatar->move($path, $avatar_nombre);
        }

        $usuario = new Tercero($request->all());

        if ($avatar) {
            $usuario->avatar = "uploads/avatar/" . $avatar_nombre;
        }

        $usuario->contraseña = bcrypt($request->contraseña);
        $usuario->tipo_id     =$request->tipo_id;
        //Tipo::select('id')->where('nombre','=','Proveedor');
       // $request->tipo_id;

       
       
        $usuario->usuario_id = currentUser()->id;
        $usuario->save();


        //$permisos = Role::findOrFail($request->rol_id)->permissions;

        //foreach ($permisos as $per) {
            //dd($per->id);
          //  $permiso = Permission::findOrFail($per->id);
            //$usuario = Tercero::findOrFail($usuario->id);
            //$usuario->attachPermission($permiso);
        //}

/*
    if(!\Input::file("file"))
    {
        return redirect('uploads')->with('error-message', 'File has required field');
    }
 
    $mime = \Input::file('file')->getMimeType();
    $extension = strtolower(\Input::file('file')->getClientOriginalExtension());
    $fileName = uniqid().'.'.$extension;
    $path = "files_uploaded";
 
    switch ($mime)
    {
        case "image/jpeg":
        case "image/png":
        case "image/gif":
        case "application/pdf":
            if (\Request::file('file')->isValid())
            {
                \Request::file('file')->move($path, $fileName);
                $upload = new Archivo();
                $upload->nombre = $fileName;
                if($upload->save())
                {
                    return redirect('uploads')->with('success-message', 'File has been uploaded');
                }
                else
                {
                    \File::delete($path."/".$fileName);
                    return redirect('uploads')->with('error-message', 'An error ocurred saving data into database');
                }
            }
        break;
        default:
            return redirect('uploads')->with('error-message', 'Extension file is not valid');
    }

     

    public function Uploads_init(Request $request) {
        //return $request;
        //return $request->size;
        //return currentUser()->id;
        $archivo = new Archivo;
        $archivo->usuario_id = currentUser()->id;
        $archivo->nombre    = $request->name;
        $archivo->tamano    = $request->size;
        $archivo->tipo_ext  = $request->type;
        $archivo->tipo_id   = $request->tipo_id;
        $archivo->save();
    }
*/
        //dd($usuario->id);

        Alert::message("! Proveedor registrado con éxito !  ", 'success');
        //return redirect()->route('admin.usuarios.index');

        return redirect()->route('admin.proveedores.index');







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

        return view('admin.proveedores.edit', compact('usuario', 'tipos','ciudades', 'oficinas', 'roles', 'clientes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
   public function update(EditaUsuarioProveedor $request, $id) {

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
       // $usuario->rol_id      = $request->rol_id; //Toco pasarla manual, por que el request no la actualizaba.
        $usuario->usuario_id  = currentUser()->id;
       // $usuario->ip          = $request->ip();
        $usuario->tipo_id    = $request->tipo_id;
        //Tipo::TipoIdenti($request->tipo_id)->get();
        $usuario->save();

        Alert::message('! Usuario ' . $usuario->nombres . " " . $usuario->apellidos . " Actualizado con éxito ! ", 'success');
        //return redirect()->route('admin.files.index', compact('tipo_id','usuario_id'));
        return redirect()->route('admin.proveedores.index');
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

        return redirect()->route('admin.proveedores.index');
    }

    protected function getusuario() {
       // $ciudades   = Ciudad::get()->lists('nombre_completo', 'id');
       $tipos= Tipo::get()->lists('nombre','id');//->toarray();
        //$oficinas =  Oficina::lists('nombre', 'id');
       // $roles      = Role::lists('name', 'id');
        //$clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
     return view('admin.proveedores.createusua', compact('tipos'));
    }

public function storeNuevo(NuevoUsuarioexterno $request) {
//ChangedProperties::select('changed_property','change_type','previous_value','updated_value')->get();
      $query=(Tercero::select ('id')->where('email','=',($request->email_Patrocinador))->get());
    
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
