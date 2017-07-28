<?php
namespace App\Http\Requests\Terceros\Proveedor;

use App\Http\Requests\Request;
use Illuminate\Routing\Route;

class EditaUsuarioProveedor extends Request {

      private $route;

    public function __construct(Route $route) {
        $this->route = $route;
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
            'identificacion' => 'numeric|required|unique:terceros,identificacion,' . $this->route->getParameter('proveedores'),
            'tipo_id'        =>'string|required',
            'nombres'        => 'string|required',
           // 'apellidos'      => 'string|required',
            'direccion'      => 'string|required',
            'telefono'       => 'integer|digits_between:7,10|required',
            'email'          => 'email|required|unique:terceros,email,' . $this->route->getParameter('proveedores'),
            'ciudad_id'      => 'integer|required',
            'usuario'        => 'required|unique:terceros,usuario,' . $this->route->getParameter('proveedores'),
            //'oficina_id'    => 'integer|required',
            //'rol_id'      => 'integer|required',
            // 'avatar'         => 'image',
        ];
    }
}
