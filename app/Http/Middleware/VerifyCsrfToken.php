<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {
        /**
         * The URIs that should be excluded from CSRF verification.
         *
         * @var array
         */
        protected $except = [
                'admin/datos_recogida',
                'admin/permisos/datos',
                'admin/permisos/asignar',
                'admin/permisos/desasignar',
                'admin/tarifas/valor',
                'admin/files/index',
                'admin/uploads_init',
                'admin/reportes/datos',
                'admin/clientes/servicios',
                'digitacion/cuenta/',
                'tarifas/valor/',
                'admin/recogidas/calendario/',
                'admin/firmar',
                'admin/uploads',
                'admin/terceros/*',
                'customers/create',
                'orders/create',
                'orders/update',
                'orders/delete'
        ];
}
