<?php

namespace App\Http\Requests\Ciudades;

use App\Http\Requests\Request;

class NuevaCiudad extends Request
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
            'codigo_dane' => 'numeric|required|unique:ciudades,codigo_dane',
            'nombre'        => 'regex:/^[a-zA-Z ]+$/|required',
            'departamento'      => 'Alpha|required',
            'region'      => 'Alpha|required',
            'tipo_id' => 'numeric|required|exists:tipos,id',
        ];
    }
}