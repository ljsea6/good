<?php
namespace App\Http\Controllers;

use App\Entities\Ciudad;
use App\Entities\Oficina;
use App\Entities\Tercero;
use App\Entities\Tipo;
use App\Entities\Network;
use App\Entities\Regla;
use App\Http\Controllers\Controller;
use App\Http\Requests\Terceros\Usuarios\EditaUsuario;
use App\Http\Requests\Reglas\EditaRegla;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuario;
use App\Http\Requests\Reglas\NuevaRegla;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuarioexterno;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;

class ReglasController extends Controller {

    /**
     * Display a listing of the  resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $permisos = Permission::lists('name', 'id');

//$usuarios = Tercero::with('ciudad')->tipoUsuario(2)->orderby('id')->paginate(10);
        //dd($usuarios);
        return view('admin.reglas.index', compact('permisos'));
    }

    public function anyData() {

 $reglas = Regla::select( 'id','id_red','nivel','valor','estado','plataforma')->orderby('id_red');

        return Datatables::of($reglas)
        ->addColumn('action', function ($reglas) {
                return '<div align=center><a href="' . route('admin.reglas.edit', $reglas->id) . '"  class="btn btn-warning btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.reglas.destroy', $reglas->id) . '"  onclick="return confirm(\'¿Seguro que deseas eliminarlo ?\')" class="btn btn-danger btn-xs">
                        <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                </a></div>';
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

    $id_red = Network::get()->lists('nombre','id_red');//->toarray();

       return view('admin.reglas.create',compact('id_red'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevaRegla $request) {

       $reglas= new Regla($request->all());
       $reglas->id_red=$request->id_red;
        $reglas->nivel=$request->nivel;
        $reglas->valor=$request->valor;
        $reglas->estado= $request->estado;
        $reglas->plataforma= $request->plataforma;
        $reglas->save();

        //dd($per->id);
       

        

        Alert::message("! Regla registrada con éxito !  ", 'success');
//If()
//{

    
//If()
//{

     return redirect()->route('admin.reglas.index');
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
        $id_red = Network::get()->lists('nombre','id_red');//->toarray();
        $reglas=Regla::findOrFail($id);

        
        return view('admin.reglas.edit', compact('reglas','id_red'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditaRegla $request, $id) {

        //dd("- ".($request->telefono)." -");
        $reglas= Regla::findOrFail($id);

        $reglas->id_red=$request->id_red;
        $reglas->nivel=$request->nivel;
        $reglas->valor=$request->valor;
        $reglas->estado=$request->estado;
        $reglas->plataforma=$request->plataforma;
        $reglas->save();
            

        Alert::message('! regla' . $reglas->id_red . " Actualizado con éxito ! ", 'success');

        return redirect()->route('admin.reglas.index');
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
