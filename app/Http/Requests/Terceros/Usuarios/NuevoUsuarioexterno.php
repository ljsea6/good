<?php
namespace App\Http\Requests\Terceros\Usuarios;

use App\Http\Requests\Request;

class NuevoUsuarioexterno extends Request {

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
          
                // 'identificacion' => 'numeric|required|unique:terceros',
            'tipo_id'        =>'string|required',
            'nombres'        => 'string|required',
            //'apellidos'      => 'string|required',
           // 'direccion'      => 'string|required',
            //'telefono'       => 'integer|digits_between:7,10|required',
            'email'          => 'email|required|unique:terceros,email',
            //'ciudad_id'      => 'integer|required',
            //'usuario'        => 'required|unique:terceros,usuario',
            'contraseña'     => 'required',
            'email_Patrocinador'=> 'email|required|exists:terceros,email',
            // 'email_Patrocinador','email|required|exists:terceros,email','message'=> 'Escribe un email valido de tu patrocinador',
         // 'cargo_id'       => 'integer|required',
        //'sucursal_id'    => 'integer|required',
            //'rol_id'      => 'integer|required',
            //'avatar'         => 'image|required',
        ];
    }
}

