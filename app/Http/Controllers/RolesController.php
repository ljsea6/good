<?php
namespace App\Http\Controllers;
use App\Entities\Roles;
use App\Entities\RolesPermisos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;
class RolesController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //$roles = Roles::paginate(10);
        return view('admin.roles.index', compact('roles'));
    }
    public function anyData() {

        $roles = Roles::select('id', 'name', 'description');
        return Datatables::of($roles)
            ->addColumn('id', function ($roles) {
                return '<div align=left>'.$roles->id.'</div>';
            })
            ->addColumn('name', function ($roles) {
                return '<div align=left>'.$roles->name.'</div>';
            })
            ->addColumn('description', function ($roles) {
                return '<div align=left>'.$roles->description.'</div>';
            })
            ->addColumn('permisos', function ($roles) {
                return '<div align=left><a data-toggle="modal" rol_id=' . $roles->id . ' data-target="#permisos" class="btn btn-primary get-permisos btn-xs" OnClick="get_permisos(' . $roles->id . ')">
                                Permisos
                </a></div>';
            })
            ->addColumn('action', function ($roles) {
                return '<div align=left><a href="' . route('admin.perfiles.edit', $roles->id) . '"  class="btn btn-warning btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.perfiles.destroy', $roles->id) . '"  onclick="return confirm(\'¿Seguro que deseas eliminarlo ?\')" class="btn btn-danger btn-xs">
                        <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                </a></div>';
            })
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.roles.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $rol             = new Roles($request->all());
        $rol->usuario_id = currentUser()->id;
        $rol->ip         = $request->ip();
        $rol->save();
        Alert::message("Rol registrado con exito! ", 'success');
        return redirect()->route('admin.roles.index');
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
        $rol = Roles::findOrFail($id);
        return view('admin.roles.edit', compact('rol'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $rol = Roles::findOrFail($id);
        $rol->fill($request->all());
        $rol->usuario_id = currentUser()->id;
        $rol->ip         = $request->ip();
        $rol->save();
        Alert::message('Rol ' . $rol->nombre . " Actualizado con exito! ", 'success');
        return redirect()->route('admin.roles.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $rol = Roles::findOrFail($id);
        $rol->delete();
        Alert::message('Rol ' . $rol->nombre . " eliminado con exito! ", 'success');
        return redirect()->route('admin.roles.index');
    }
    public function permisos($id) {
        $rol = Roles::findOrFail($id);
        //$permisos =   RolesPermisos::with(['permiso'])->where('rol_id', '=', $id)->get(array('permiso.ruta'));
        $permisos = RolesPermisos::with('permiso')->
        where('rol_id', '=', $id)->
        get()->toArray();
        $rutas = [];
//Pasamos un arreglo on los permisos para poderlo validar en la vista
        foreach ($permisos as $permiso) {
            array_push($rutas, $permiso['permiso']);
        }
        return view('admin.roles.permisos', compact('rutas'));
    }
}