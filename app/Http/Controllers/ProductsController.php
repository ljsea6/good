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
    function verify_webhook($data, $hmac_header)
    {
          $calculated_hmac = base64_encode(hash_hmac('sha256', $data, 'afc86df7e11dcbe0ab414fa158ac1767', true));
          return hash_equals($hmac_header, $calculated_hmac);
    }

    public function welcome()
    {
        return view('admin.reportes.products');
    }
    
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

        return Datatables::of($send)
           
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
            ->addColumn('tipo_producto', function ($send) {
                return '<div align=left>' . $send->tipo_producto . '</div>';
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
        $input = file_get_contents('php://input');
        
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($input), $hmac_header);
        $resultapi = error_log('Webhook verified: '.var_export($verified, true));
        
        if ($resultapi == 'true') {
            $product = json_decode($input, true);
        
            $response = Product::find((int)$product['id']);

            if(count($response) == 0) {
                    Product::create([
                        'body_html' => $product['body_html'],
                        'created_at' => Carbon::parse($product['created_at']),
                        'handle' => $product['handle'],
                        'id' => $product['id'],
                        'image' => $product['image'],
                        'images' => $product['images'],
                        'options' => $product['options'],
                        'product_type' => $product['product_type'],
                        'published_at' => Carbon::parse($product['published_at']),
                        'published_scope' => $product['published_scope'],
                        'tags' => $product['tags'],
                        'template_suffix' => ($product['template_suffix'] !== null ) ? $product['template_suffix'] : null,
                        'title' => $product['title'],
                        'metafields_global_title_tag' => (isset($product['metafields_global_title_tag'])) ? $product['metafields_global_title_tag'] : null,
                        'metafields_global_description_tag' => (isset($product['metafields_global_description_tag'])) ? $product['metafields_global_description_tag'] : null,
                        'updated_at' => Carbon::parse($product['updated_at']),
                        'variants' => $product['variants'],
                        'vendor' => $product['vendor'],
                    ]);

                    return response()->json(['status' => 'The resource is created successfully'], 200);
            } else {
                return response()->json(['status' => 'The resource is not process'], 200);
            }
        }
        
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
