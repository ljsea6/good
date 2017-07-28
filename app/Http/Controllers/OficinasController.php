<?php
namespace App\Http\Controllers;

use App\Entities\Ciudad;
use App\Entities\Resolucion;
use App\Entities\Oficina;
use App\Http\Controllers\Controller;
use App\Http\Requests\Oficinas\EditaOficina;
use App\Http\Requests\Oficinas\NuevaOficina;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;

class OficinasController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //$oficinas = Sucursal::with('ciudad')->paginate(10);
        return view('admin.oficinas.index');
    }

    public function anyData() {
        // dd(Datatables::of(Estado::select('*'))->make(true));

        $oficinas = Oficina::select('oficinas.id', 'oficinas.nombre', 'c.nombre as ciudad', 'oficinas.direccion', 'oficinas.telefono')->leftjoin('ciudades as c', 'oficinas.ciudad_id', '=', 'c.id');
        return Datatables::of($oficinas)
            ->addColumn('action', function ($oficinas) {
                return '<div align=center><a href="' . route('admin.oficinas.edit', $oficinas->id) . '"  class="btn btn-warning  btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.oficinas.destroy', $oficinas->id) . '"  onclick="return confirm(\'¿Seguro que deseas eliminarlo ?\')" class="btn btn-danger  btn-xs">
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
        $ciudades           = Ciudad::get()->lists('nombre_completo', 'id');
        $resoluciones = Resolucion::lists('nombre', 'id');

        return view('admin.oficinas.create', compact('ciudades', 'resoluciones'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevaOficina $request) {
        $Oficina             = new Oficina($request->all());
        $Oficina->usuario_id = currentUser()->id;
        $Oficina->ip         = $request->ip();
        $Oficina->save();

        Alert::message("Oficina registrado con exito! ", 'success');

        return redirect()->route('admin.oficinas.index');
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
        $Oficina     = Oficina::findOrFail($id);
        $ciudades     = Ciudad::lists('nombre', 'id');
        $resoluciones = Resolucion::lists('nombre', 'id');

        return view('admin.oficinas.edit', compact('Oficina', 'ciudades', 'resoluciones'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditaOficina $request, $id) {
        $Oficina = Oficina::findOrFail($id);
        $Oficina->fill($request->all());
        $Oficina->usuario_id = currentUser()->id;
        $Oficina->ip         = $request->ip();
        $Oficina->save();

        Alert::message('Oficina ' . $Oficina->nombre . " Actualizado con exito! ", 'success');

        return redirect()->route('admin.oficinas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $Oficina = Oficina::findOrFail($id);
        $Oficina->delete();

        Alert::message('Oficina ' . $Oficina->nombre . " eliminado con exito! ", 'success');

        return redirect()->route('admin.oficinas.index');
    }
}
