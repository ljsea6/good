<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Product;
use Carbon\Carbon;


class ProductsController extends Controller
{
    /**
     * Undocumented function
     *
     * @return Products
     */

    public function index()
    {
        return view('admin.productos.home');
    }
    
    public function anyData()
    {
        $products = Product::select('id', 'title', 'precio_unidad', 'unidades_vendidas', 'porcentaje')
                ->where('unidades_vendidas', '>', 0)
                ->get();
        
        $send = collect($products);

        return Datatables::of($send )
           
            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send['id'] . '</div>';
            })
            ->addColumn('title', function ($send) {
                return '<div align=left>' . $send['title'] . '</div>';
            })
            ->addColumn('precio_unidad', function ($send) {
                return '<div align=left>' . number_format($send['precio_unidad']) . '</div>';
            })
            ->addColumn('unidades_vendidas', function ($send) {
                return '<div align=left>' . $send['unidades_vendidas'] . '</div>';
            })
            ->addColumn('porcentaje', function ($send) {
                return '<div align=left><input id='.$send['id'].' name='.$send['id'].' type=number min=0 max=100 value=' . $send['porcentaje'] . ' ></div>';
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       if (isset($request['value'])) {
           $datas = explode('&', $request['value']);
            foreach ($datas as $data) {
                $result = explode('=', $data);
                $product = Product::find($result[0]);

                if ($result[1] !== "") {
                    $value = (int)$result[1];
                    
                    if ($value <= 100) {
                        $product->porcentaje = $result[1];
                        $product->save();
                    } else {
                        return response()->json(['data' => 'No se permiten valores mayores a 100. Verifique sus datos']);
                    }
                }else {
                    $product->porcentaje = null;
                    $product->save();
                }
            }
            
            return response()->json(['data' => 'actualización terminada']);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
