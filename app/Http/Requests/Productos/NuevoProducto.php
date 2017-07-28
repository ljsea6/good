<?php

namespace App\Http\Requests\Productos;

use App\Http\Requests\Request;

class NuevoProducto extends Request
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
            'nombre'        => 'string|required',
            'descripcion'      => 'string|required',
            'tipo_id' => 'numeric|required|exists:tipos,id',
        ];
    }
}