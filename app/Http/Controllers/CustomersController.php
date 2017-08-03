<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 500);

use App\Customer;
use App\Order;
use App\Entities\Network;
use App\Entities\Tercero;
use Illuminate\Http\Request;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

  

    public function getCostumers()
    {
        $referidos  = DB::table('terceros')
                            ->where('numero_referidos', '>', 0)
                            ->where('numero_ordenes_referidos', '>', 0)
                            ->where('total_price_orders', '>', 0)
                            ->select('id', 'nombres', 'total_price_orders')
                            ->orderBy('total_price_orders', 'desc')
                            ->get();
        return $referidos;
    }
       
    

    public function index()
    {
        return Test::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
    public function update(Request $request, $id)
    {
        //
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
