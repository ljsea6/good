<?php
namespace App\Http\Requests\Reglas;

use App\Http\Requests\Request;
use Illuminate\Routing\Route;

class EditaRegla extends Request {


   private $route;

    public function __construct(Route $route) {
            $this->route = $route ;

             }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [

           
            'id_red' => 'string|required',
            'nivel' =>'numeric|required',
            'valor'  => 'numeric|required',
            'estado'  => 'string|required',
            'plataforma'  => 'string|required'
        ];
    }
}
