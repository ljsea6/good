<?php
namespace App\Http\Controllers;

use App\Entities\Ciudad;
use App\Entities\Tipo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ciudades\EditaCiudad;
use App\Http\Requests\Ciudades\NuevaCiudad;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;

class CiudadesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //$ciudades = Ciudad::paginate(10);
        return view('admin.ciudades.index');
    }

    public function anyData() {
        // dd(Datatables::of(Estado::select('*'))->make(true));

        $ciudades = Ciudad::select('id', 'codigo_dane', 'nombre', 'departamento', 'region');
        return Datatables::of($ciudades)
            ->addColumn('action', function ($ciudades) {
                return '<div align=center><a href="' . route('admin.ciudades.edit', $ciudades->id) . '"  class="btn btn-warning btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.ciudades.destroy', $ciudades->id) . '"  onclick="return confirm(\'¿Seguro que deseas eliminarlo ?\')" class="btn btn-danger btn-xs">
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

        $tipos_ciudades = Tipo::tipoDestino()->lists('nombre', 'id');

        return view('admin.ciudades.create', compact('tipos_ciudades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevaCiudad $request) {
        $ciudad             = new Ciudad($request->all());
        $ciudad->ip         = $request->ip();
        $ciudad->usuario_id = currentUser()->id;
        $ciudad->save();

        Alert::message("Ciudad registrada con exito! ", "success");

        return redirect()->route('admin.ciudades.index');
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
        $ciudad         = Ciudad::findOrFail($id);
        $tipos_ciudades = Tipo::tipoDestino()->lists('nombre', 'id');

        return view('admin.ciudades.edit', compact('ciudad'), compact('tipos_ciudades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditaCiudad $request, $id) {
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->fill($request->all());
        $ciudad->ip         = $request->ip();
        $ciudad->usuario_id = currentUser()->id;
        $ciudad->save();

        Alert::message('Ciudad ' . $ciudad->ciudad . " Actualizada con exito! ", 'success');

        return redirect()->route('admin.ciudades.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $ciudad = Ciudad::findOrFail($id);
        $ciudad->delete();

        Alert::message('Ciudad ' . $ciudad->ciudad . " eliminada con exito! ", 'success');

        return redirect()->route('admin.ciudades.index');
    }
}
