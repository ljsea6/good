<?php
namespace App\Http\Controllers;

use App\Entities\Tipo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dominios\EditaDominio;
use App\Http\Requests\Dominios\NuevoDominio;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;

class DominiosController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin.dominios.index');
    }

    public function anyData() {
        // dd(Datatables::of(Estado::select('*'))->make(true));

        $dominios = Tipo::select('tipos.id', 'tipos.nombre', 't.nombre as padre_id')->leftjoin('tipos as t', 'tipos.padre_id', '=', 't.id');

        return Datatables::of($dominios)
            ->addColumn('action', function ($dominios) {
                return '<div align=center><a href="' . route('admin.dominios.edit', $dominios->id) . '"  class="btn btn-warning btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.dominios.destroy', $dominios->id) . '"  onclick="return confirm(\'¿Seguro que deseas eliminarlo ?\')" class="btn btn-danger btn-xs">
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
        $dominios = Tipo::whereNull('padre_id')->lists('nombre', 'id');

        return view('admin.dominios.create', compact('dominios'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevoDominio $request) {
        $dominio = new Tipo($request->all());
        $dominio->save();

        Alert::message("Dominio registrado con exito! ", 'success');

        return redirect()->route('admin.dominios.index');
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
        $dominio  = Tipo::findOrFail($id);
        $dominios = Tipo::whereNull('padre_id')->lists('nombre', 'id');

        return view('admin.dominios.edit', compact('dominio', 'dominios'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditaDominio $request, $id) {
        $dominio = Tipo::findOrFail($id);
        $dominio->fill($request->all());
        $dominio->save();

        Alert::message('Dominio ' . $dominio->nombre . " Actualizado con exito! ", 'success');

        return redirect()->route('admin.dominios.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $dominio = Tipo::findOrFail($id);
        $dominio->delete();

        Alert::message('Dominio ' . $dominio->nombre . " eliminado con exito! ", 'success');

        return redirect()->route('admin.dominios.index');
    }
}
