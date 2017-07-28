<?php

namespace App\Http\Requests\Resoluciones;

use App\Http\Requests\Request;
use Illuminate\Routing\Route;

class EditaResolucion extends Request
{

    private $route;

    public function __construct(Route $route) {
            $this->route = $route ;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'numero' => 'numeric|required|unique:resoluciones,numero,'.$this->route->getParameter('resoluciones') ,
            'nombre'        => 'string|required',
            'digitos'        => 'integer|required',
            'desde'        => 'string|required',
            'hasta'        => 'string|required',
            'vencimiento'        => 'date|required',
        ];
    }
}
