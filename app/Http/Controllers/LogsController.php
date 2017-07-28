<?php
namespace App\Http\Controllers;

use App\Entities\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;

class LogsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $logs = Log::paginate(10);
        return view('admin.logs.index', compact('logs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.servicios.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $servicio = new Servicio($request->all());
        $servicio->save();

        Alert::message("Servicio registrado con exito! ", 'success');

        return redirect()->route('admin.servicios.index');
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
        $servicio = Servicio::findOrFail($id);

        return view('admin.servicios.edit', compact('servicio'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $servicio = Servicio::findOrFail($id);
        $servicio->fill($request->all());
        $servicio->save();

        Alert::message('Servicio ' . $servicio->nombre . " Actualizado con exito! ", 'success');

        return redirect()->route('admin.servicios.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        Alert::message('Servicio ' . $usuario->nombre . " eliminado con exito! ", 'success');

        return redirect()->route('admin.servicios.index');
    }
}
