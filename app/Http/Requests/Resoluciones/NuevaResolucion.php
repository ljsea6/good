<?php

namespace App\Http\Requests\Resoluciones;

use App\Http\Requests\Request;

class NuevaResolucion extends Request
{
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
            'numero'        => 'string|required',
            'nombre'        => 'string|required',
            'digitos'        => 'integer|required',
            'desde'        => 'string|required',
            'hasta'        => 'string|required',
            'vencimiento'        => 'date|required',
        ];
    }
}