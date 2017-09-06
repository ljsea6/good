<?php

namespace App\Http\Controllers;

use App\Entities\Tercero;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Commision;
use Yajra\Datatables\Datatables;

class CommissionsController extends Controller
{
    public function anyData()
    {
        $commissions = Commision::all();

        $send = collect($commissions);

        return Datatables::of($send )
            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send->id . '</div>';
            })
            ->addColumn('customer', function ($send) {
                $tercero = Tercero::find($send->tercero_id);
                return '<div align=left>' . $tercero->nombres . '</div>';
            })
            ->addColumn('code', function ($send) {
                $tercero = Tercero::find($send->tercero_id);
                return '<div align=left>' . $tercero->email . '</div>';
            })
            ->addColumn('gift', function ($send) {

                if (count($send->gift_card['form_params']['gift_card']) > 0) {

                    return '
                  
                    <div class="text-left">
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal'. $send->id .'">'. $send->id .'</button>
                        <!-- Modal -->
                        <div id="myModal'. $send->id .'" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" style="color: #f60620">#Cliente '. $send->gift_card['form_params']['gift_card']['customer_id'] .'</h4>
                                    </div>
                                    <div class="modal-body">
       
                                           <h4 class="media-heading">Total: ' . number_format($send->gift_card['form_params']['gift_card']['initial_value']) . '</h4>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                                           
                ';


                }

            })
            ->addColumn('order', function ($send) {
                $result = '';
                if (count($send->orders) > 0) {
                    foreach ($send->orders as $order) {
                        $result .= '<div class="container">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                         <p><strong># Orden Id ' . $order['order_id'] . '</strong></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <!-- Left-aligned media object -->
                                                        <div class="media">
                                                            
                                                            <div class="media-body">
                                                                <p>Orden ' . $order['name'] . '</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <hr>';
                    }
                }

                return '
                  
                    <div class="text-left">
                        <button style="color: #f60620" class="btn-link" data-toggle="modal" data-target="#myModal-'. $send->id .'">Detalle</button>
                        <!-- Modal -->
                        <div id="myModal-'. $send->id .'" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
    
                                    </div>
                                    <div class="modal-body">
                                           '.$result.'
                                           
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                                           
                ';
            })
            ->addColumn('value', function ($send) {
                return '<div align=left>' . number_format($send->value) . '</div>';
            })
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.gifts.index');
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
