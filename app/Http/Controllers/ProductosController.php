<?php
namespace App\Http\Controllers;

use App\Entities\Producto;
use App\Entities\Tipo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Productos\EditaProducto;
use App\Http\Requests\Productos\NuevoProducto;
use PhpParser\Node\Stmt\Return_;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;
use App\Product;
use App\Customer;
use Carbon\Carbon;

class ProductosController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //$products = Product::all();
        $products = array();
        $customers = Customer::select('id', 'first_name', 'last_name', 'email', 'created_at')->get();

        $day = (int)Carbon::parse(Carbon::now()->subDays(1))->format('d');
        $month = (int)Carbon::parse(Carbon::now()->subDays(1))->format('m');
        $year = (int)Carbon::parse(Carbon::now()->subDays(1))->format('Y');

        foreach ($customers as $customer) {
            $postDay = (int)$customer->created_at->format('d');
            $postMonth = (int)$customer->created_at->format('m');
            $postYear = (int)$customer->created_at->format('Y');
            if($postDay > $day && $postMonth == $month && $postYear == $year) {
                array_push($products, $customer);
            }
        }

        //return $result;
        //$products =  Customer::where('last_name', 'rcn')->select('id', 'first_name', 'email', 'last_name', 'created_at')->orderby('created_at', 'desc')->take(5)->get();

        return view('admin.productos.welcome', compact('products'));
    }

    public function anyData() {

        $productos = Producto::select('productos.id', 'productos.nombre', 'productos.descripcion', 't.nombre as tipo')->leftjoin('tipos as t', 'productos.tipo_id', '=', 't.id');
        return Datatables::of($productos)
            ->addColumn('action', function ($productos) {
                return '<div align=center><a href="' . route('admin.productos.edit', $productos->id) . '"  class="btn btn-warning  btn-xs">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                </a>
                <a href="' . route('admin.productos.destroy', $productos->id) . '"  onclick="return confirm(\'¿ Desea eliminar el registro seleccionado ?\')" class="btn btn-danger  btn-xs">
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
        $tipos = Tipo::lists('nombre', 'id');

        return view('admin.productos.create', compact('tipos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevoProducto $request) {

        $producto             = new Producto($request->all());
        $producto->usuario_id = currentUser()->id;
        $producto->ip         = $request->ip();
        $producto->save();

        Alert::message("¡ Producto registrado con éxito ! ", 'success');

        return redirect()->route('admin.productos.index');
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

        $producto = Producto::findOrFail($id);
        $tipos    = Tipo::lists('nombre', 'id');

        return view('admin.productos.edit', compact('producto', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditaProducto $request, $id) {

        $producto = Producto::findOrFail($id);
        $producto->fill($request->all());
        $producto->usuario_id = currentUser()->id;
        $producto->ip         = $request->ip();
        $producto->save();

        Alert::message('¡ Producto ' . $producto->nombre . " actualizado con éxito ! ", 'success');

        return redirect()->route('admin.productos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        Alert::message('¡ Producto ' . $producto->nombre . " eliminado con éxito ! ", 'success');

        return redirect()->route('admin.productos.index');
    }
}
