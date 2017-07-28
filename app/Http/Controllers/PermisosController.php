<?php
namespace App\Http\Controllers;

use App\Entities\Tercero;
use App\Http\Controllers\Controller;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;

class PermisosController extends Controller {

    public function datos(Request $request) {

        switch ($request->seccion) {
            case 'perfiles':
                $rol_permisos = Role::findOrFail($request->id)->permissions->lists('name', 'id')->toArray();
                return response()->json($rol_permisos);
                break;
            case 'terceros':
                $tercero_permisos = Tercero::findOrFail($request->id)->userPermissions->lists('name', 'id')->toArray();

                if ($tercero_permisos === '') {
                    return '0';
                } else {
                    return response()->json($tercero_permisos);
                }

                break;
        }

    }

    public function asignar(Request $request) {

        switch ($request->seccion) {
            case 'perfiles':
                $permiso = Permission::findOrFail($request->permission_id);
                $perfil  = Role::findOrFail($request->role_id);
                $perfil->attachPermission($permiso);
                return response()->json('Permiso asignado!');
                break;
            case 'terceros':
                $permiso = Permission::findOrFail($request->permission_id);
                $usuario = Tercero::findOrFail($request->tercero_id);
                $usuario->attachPermission($permiso);

                return response()->json('Permiso asignado!');
                break;
        }

    }

    public function desasignar(Request $request) {

        switch ($request->seccion) {
            case 'perfiles':
                $permiso = Permission::findOrFail($request->permission_id);
                $role    = Role::findOrFail($request->role_id);
                $role->detachPermission($permiso);
                return response()->json('Permiso desasignado!');
                break;
            case 'terceros':
                $permiso = Permission::findOrFail($request->permission_id);
                $tercero = Tercero::findOrFail($request->tercero_id);
                $tercero->detachPermission($permiso);
                return response()->json('Permiso desasignado!');
                break;
        }

    }

}
