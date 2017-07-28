<?php
namespace App\Http\Controllers;

use App\Entities\Ciudad;
use App\Entities\Oficina;
use App\Entities\Tercero;
use App\Entities\Tipo;
use App\Entities\Network;
use App\Entities\Tercero_network;
use App\Entities\Comision_detalle;
use App\Entities\Regla;
use App\Entities\Comision;
use App\Http\Controllers\Controller;
use App\Http\Requests\Terceros\Usuarios\EditaUsuario;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuario;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuarioexterno;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;

class ComisionesController extends Controller {

    /**
     * Display a listing of the  resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $permisos = Permission::lists('name', 'id');

//$usuarios = Tercero::with('ciudad')->tipoUsuario(2)->orderby('id')->paginate(10);
        //dd($usuarios);
        return view('admin.comisiones.index', compact('permisos'));
    }

    public function anyData() {
 

        $usuarios = Comision_detalle::select('e.id', 'c.identificacion', 'comisiones_detalles.id_transaccion','r.id as regla', 'c.usuario', 'e.fecha', 'comisiones_detalles.valor', 'comisiones_detalles.estado')->leftjoin('terceros as c', 'c.id', '=', 'comisiones_detalles.id_tercero')->leftjoin('reglas as r', 'r.id', '=', 'comisiones_detalles.id_regla')->leftjoin('comisiones as e', 'e.id', '=', 'comisiones_detalles.id_comision')->orderby('e.id');

        return Datatables::of($usuarios)
       
            ->addColumn('action', function ($usuarios) {
                return '
                <a href="' . route('admin.comisiones.edit', $usuarios->id) . '"  class="btn btn-warning btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.comisiones.destroy', $usuarios->id) . '"  onclick="return confirm(\'¿ Desea eliminar el registro seleccionado ?\')" class="btn btn-danger btn-xs">
                        <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                </a>';
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
    public function create() {



        $Regla= Regla::get()->lists('nivel', 'id');
       $tipos = Tipo::get()->lists('nombre','id');//->toarray();
        $oficinas =  Oficina::lists('nombre', 'id');
        $roles      = Role::lists('name', 'id');
        $clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
        $red=Network::lists('nombre','id_red');

//$tipos=tipo::select('id','nombre','padre_id')->leftjoin('terceros as a', 'a.tipo_id','=','tipos.id');
  //      return datatables::of($tipos)
        return view('admin.comisiones.create', compact('Regla','tipos', 'oficinas', 'roles', 'clientes','red'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevoUsuario $request) {

        $avatar = $request->file('avatar');
         $query=(Tercero::select ('id')->where('email','=',($request->email_Patrocinador))->get());
         
        if ($avatar) {
            $avatar_nombre = str_random(30) . "." . $avatar->getClientOriginalExtension();
            $path          = public_path() . "/uploads/avatar/";
            $avatar->move($path, $avatar_nombre);
        }

        $usuario = new Tercero($request->all());
        $red = new Tercero_network($request->all());
        

        if ($avatar) {
            $usuario->avatar = "uploads/avatar/" . $avatar_nombre;
        }

        $usuario->contraseña = bcrypt($request->contraseña);
        $usuario->tipo_id     =$request->tipo_id;
       

        if ($request->cliente) {
            $usuario->padre_id = $request->cliente;
        }
         $red->id_padre =$query[0]->id;
       
        $red->id_red=$request->id_red;
        $usuario->rol_id     = $request->rol_id; //Toco pasarla manual, por que el request no la actualizaba.
        $usuario->usuario_id = currentUser()->id;
      
        $usuario->ip         = $request->ip();
        //$usuario->tipo_id    = $request->tipo_id;
        
        $usuario->save();
        $Variable=(Tercero::select ('id')->max('id'));
         $red->id_tercero=($Variable);
        $red->save();

        $permisos = Role::findOrFail($request->rol_id)->permissions;

        foreach ($permisos as $per) {
            //dd($per->id);
            $permiso = Permission::findOrFail($per->id);
            $usuario = Tercero::findOrFail($usuario->id);
            $usuario->attachPermission($permiso);
        }

        //dd($usuario->id);

        Alert::message("! Usuario registrado con éxito !  ", 'success');
//If()
//{

     return redirect()->route('admin.usuarios.index');
//} else {

  //  return redirect()->route('admin.proveedores.index');
//}

       
    


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

   




}
