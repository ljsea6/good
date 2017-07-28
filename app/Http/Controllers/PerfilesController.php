<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;

class PerfilesController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $permisos = Permission::lists('name', 'id');
        $perfiles = Role::all();
        return view('admin.perfiles.index', compact('perfiles', 'permisos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.perfiles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'regex:/^[a-zA-Z ]+$/|required',
        ]);

        $perfil             = new Role($request->all());
        $perfil->usuario_id = currentUser()->id;
        $perfil->ip         = $request->ip();
        $perfil->save();

        Alert::message("Perfil registrado con exito! ", 'success');

        return redirect()->route('admin.perfiles.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $perfil = Role::findOrFail($id);
        return view('admin.perfiles.edit', compact('perfil'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $this->validate($request, [
            'name' => 'regex:/^[a-zA-Z ]+$/|required',
        ]);

        $perfil = Role::findOrFail($id);
        $perfil->fill($request->all());
        $perfil->usuario_id = currentUser()->id;
        $perfil->ip         = $request->ip();
        $perfil->save();

        Alert::message('Perfil ' . $perfil->name . " actualizado con éxito! ", 'success');

        return redirect()->route('admin.perfiles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $perfil = Role::findOrFail($id);
        $perfil->delete();

        Alert::message('Perfil ' . $perfil->nombre . " eliminado con exito! ", 'success');

        return redirect()->route('admin.perfiles.index');
    }
}
