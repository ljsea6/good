<?php

namespace App\Http\Requests\Dominios;

use App\Http\Requests\Request;
use Illuminate\Routing\Route;

class EditaDominio extends Request
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
            'nombre'        => 'string|required',
            'padre_id'        => 'integer',
        ];
    }
}
