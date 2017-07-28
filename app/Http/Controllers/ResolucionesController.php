<?php
namespace App\Http\Controllers;

use App\Entities\Resolucion;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resoluciones\EditaResolucion;
use App\Http\Requests\Resoluciones\NuevaResolucion;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;

class ResolucionesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //$resoluciones = Resolucion::paginate(10);
        return view('admin.resoluciones.index');
    }

    public function anyData() {
        // dd(Datatables::of(Estado::select('*'))->make(true));

        $resoluciones = Resolucion::select('id', 'nombre', 'prefijo', 'numero', 'desde', 'hasta');
        return Datatables::of($resoluciones)
            ->addColumn('action', function ($resoluciones) {
                return '<div align=center><a href="' . route('admin.resoluciones.edit', $resoluciones->id) . '"  class="btn btn-warning  btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.resoluciones.destroy', $resoluciones->id) . '"  onclick="return confirm(\'¿Seguro que deseas eliminarlo ?\')" class="btn btn-danger  btn-xs">
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
        return view('admin.resoluciones.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevaResolucion $request) {
        $this->validate($request, [
            'nombre'      => 'string|required',
            'prefijo'     => 'string',
            'digitos'     => 'integer|required',
            'desde'       => 'numeric|required',
            'hasta'       => 'numeric|required',
            'vencimiento' => 'date|required',
        ]);

        $resoluciones             = new Resolucion($request->all());
        $resoluciones->usuario_id = currentUser()->id;
        $resoluciones->ip         = $request->ip();
        $resoluciones->save();

        Alert::message("Resolucion registrado con exito! ", 'success');

        return redirect()->route('admin.resoluciones.index');
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

        $resolucion = Resolucion::findOrFail($id);

        return view('admin.resoluciones.edit', compact('resolucion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditaResolucion $request, $id) {
        $this->validate($request, [
            'nombre'      => 'string|required',
            'prefijo'     => 'string',
            'digitos'     => 'integer|required',
            'desde'       => 'numeric|required',
            'hasta'       => 'numeric|required',
            'vencimiento' => 'date|required',
        ]);

        $resolucion = Resolucion::findOrFail($id);
        $resolucion->fill($request->all());
        $resolucion->usuario_id = currentUser()->id;
        $resolucion->ip         = $request->ip();
        $resolucion->save();

        Alert::message('Resolucion ' . $resolucion->nombre . " actualizada con exito! ", 'success');

        return redirect()->route('admin.resoluciones.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $resolucion = Resolucion::findOrFail($id);
        $resolucion->delete();

        Alert::message('Resolucion ' . $resolucion->nombre . " eliminada con exito! ", 'success');

        return redirect()->route('admin.resoluciones.index');
    }
}
