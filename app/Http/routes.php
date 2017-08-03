<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */
//<a href="{{ route('reset') }}">Olvido contraseña?</a>

//Pdfs
Route::get('pdf', 'PdfController@invoice');
Route::get('terceros/data', 'TercerosController@anyData');
Route::get('customers', 'CustomersController@getCostumers');
Route::get('tests', 'CustomersController@index');
Route::get('orders', 'OrdersController@index');
Route::get('products', 'ProductsController@getProducts')->name('products');
Route::get('products/count', 'ProductsController@countAllProducts');
Route::get('products/variants/price/zero', 'ProductsController@ProductsWithVariantsPriceZero');
Route::get('products/variants/price/no-zero', 'ProductsController@ProductsWithVariantsPriceNotZero');
// Authentication routes...
Route::get('/', ['as' => 'login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('/', ['as' => 'login', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);
// Password reset link
Route::get('olvido-contraseña', ['as' => 'reset', 'uses' => 'auth\PasswordController@getEmail']);
Route::post('olvido-contraseña', ['as' => 'reset', 'uses' => 'auth\PasswordController@postEmail']);
//Registrar nuevo usuario
Route::get('Registarse', ['as' => 'Registro', 'uses' => 'UsuariosController@getusuario']);
route::post('registro', ['uses' => 'UsuariosController@storenuevo', 'as' => 'admin.usuarios.storenuevo']);
// Password reset
Route::get('recuperar-contraseña/{token}', ['as' => 'recuperar', 'uses' => 'Auth\PasswordController@getReset']);
Route::post('recuperar-contraseña', ['as' => 'recuperar', 'uses' => 'Auth\PasswordController@postReset']);
Route::get('registro/payu', [ 'as' => 'PayuController@paybefore', 'as' =>'admin.payu.payu']);
//Route::get('registro/payu', [ 'as' => 'pay', 'uses' =>'PayuController@pay']);
    //payu
    Route::get('/pay', ['as' => 'pay', 'uses' => 'PaymentController@pay']); # You will need one more.
    Route::get('/payment/status', ['as' => 'payment_status', 'uses' => 'PaymentController@status']); /** * Using Named Routs to demonstrate all the possibilities. */ 
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {

    Route::get('/', ['uses' => 'AdminController@index', 'as' => 'admin.index']);
    Route::get('/network', ['uses' => 'AdminController@network', 'as' => 'admin.network']);
    Route::get('/search', ['uses' => 'AdminController@search', 'as' => 'admin.search']);
    Route::post('/finder', ['uses' => 'AdminController@finder', 'as' => 'admin.finder']);
    
    Route::get('/send/email', ['uses' => 'AdminController@email', 'as' => 'admin.send.mail']);
    Route::get('/send/msm', ['uses' => 'AdminController@msm', 'as' => 'admin.send.msm']);
    Route::post('/send', ['uses' => 'AdminController@send', 'as' => 'admin.send']);

    Route::post('/buscar', ['uses' => 'AdminController@buscar', 'as' => 'admin.buscar']);

    Route::get('/feredidos', ['uses' => 'AdminController@anyData', 'as' => 'admin.referidos']);
    // Usuarios
    Route::get('usuarios/data', ['uses' => 'UsuariosController@anyData', 'as' => 'usuarios.data']);
    Route::resource('usuarios', 'UsuariosController');
    Route::get('usuarios/{id}/destroy', ['uses' => 'UsuariosController@destroy', 'as' => 'admin.usuarios.destroy']);
    Route::get('usuarios/{id}/hijos', ['uses' => 'UsuariosController@hijos', 'as' => 'admin.usuarios.hijos']);
    //Proveedores
    Route::get('proveedores', ['uses' => 'AdminController@indexprovedores', 'as' => 'admin.proveedores.index']);
    Route::get('proveedores/data', ['uses' => 'ProveedoresController@anyData', 'as' => 'Proveedores.data']);
    Route::get('proveedores', ['uses' => 'ProveedoresController@create', 'as' => 'admin.proveedores.create']);
    //Route::get('proveedores/update', ['uses' => 'ProveedoresController@update', 'as' => 'admin.Proveedores.update']);
    Route::resource('proveedores', 'ProveedoresController');
    Route::get('Proveedores/{id}/destroy', ['uses' => 'ProveedoresController@destroy', 'as' => 'admin.proveedores.destroy']);
    Route::get('Proveedores/{id}/hijos', ['uses' => 'ProveedoresController@hijos', 'as' => 'admin.proveedores.hijos']);
    //Redes
    Route::get('networks', ['uses' => 'NetworksController@index', 'as' => 'admin.networks.index']);
    
    Route::get('networks/data', ['uses' => 'NetworksController@anyData', 'as' => 'admin.networks.data']);
    Route::get('networks/create', ['uses' => 'NetworksController@create', 'as' => 'admin.networks.create']);
    Route::resource('networks', 'NetworksController');
    //Route::get('networks', ['uses' => 'NetworksController@create', 'as' => 'admin.networks.create']);
     //Reglas
    Route::get('reglas', ['uses' => 'ReglasController@index', 'as' => 'admin.reglas.index']);
    Route::get('reglas/data', ['uses' => 'ReglasController@anyData', 'as' => 'reglas.data']);
    Route::get('reglas/create', ['uses' => 'ReglasController@create', 'as' => 'admin.reglas.create']);
    Route::resource('reglas', 'ReglasController');
    //Route::get('proveedores/update', ['uses' => 'ProveedoresController@update', 'as' => 'admin.Proveedores.update']);
    Route::resource('proveedores', 'ProveedoresController');
    Route::get('Reglas/{id}/destroy', ['uses' => 'ReglasController@destroy', 'as' => 'admin.usuarios.destroy']);
    Route::get('Reglas/{id}/hijos', ['uses' => 'ReglasController@hijos', 'as' => 'admin.usuarios.hijos']);
   //comisiones
    Route::get('comisiones', ['uses' => 'ComisionesController@index', 'as' => 'admin.comisiones.index']);
    Route::get('comisiones/data', ['uses' => 'ComisionesController@anyData', 'as' => 'comisiones.data']);
    Route::get('comisiones/create', ['uses' => 'ComisionesController@create', 'as' => 'admin.comisiones.create']);
    Route::resource('comisiones', 'comisionesController');
    //Route::get('proveedores/update', ['uses' => 'ProveedoresController@update', 'as' => 'admin.Proveedores.update']);
    Route::resource('proveedores', 'ProveedoresController');
    Route::get('Proveedores/{id}/destroy', ['uses' => 'UsuariosController@destroy', 'as' => 'admin.usuarios.destroy']);
    Route::get('Proveedores/{id}/hijos', ['uses' => 'UsuariosController@hijos', 'as' => 'admin.usuarios.hijos']);
    //Mensajeros
    Route::get('mensajeros/data', ['uses' => 'MensajerosController@anyData', 'as' => 'mensajeros.data']);
    Route::resource('mensajeros', 'MensajerosController');
    Route::get('mensajeros/{id}/destroy', ['uses' => 'MensajerosController@destroy', 'as' => 'admin.mensajeros.destroy']);
    //Clientes
    Route::post('cliente/crear', ['as' => 'cliente.crear', 'uses' => 'ClientesController@crear_landing']);
    Route::get('clientes/data', ['uses' => 'ClientesController@anyData', 'as' => 'clientes.data']);
    Route::any('clientes/servicios', ['uses' => 'ClientesController@servicios', 'as' => 'clientes.servicios']);
    Route::resource('clientes', 'ClientesController');
    Route::get('clientes/{id}/destroy', ['uses' => 'ClientesController@destroy', 'as' => 'admin.clientes.destroy']);
    // Clientes
    Route::get('couriers/data', ['uses' => 'CourierController@anyData', 'as' => 'couriers.data']);
    Route::resource('couriers', 'CourierController');
    Route::get('couriers/{id}/destroy', ['uses' => 'CourierController@destroy', 'as' => 'admin.couriers.destroy']);
    // Tarifas
    Route::resource('tarifas', 'TarifasController');
    Route::post('tarifas.masivos', ['uses' => 'TarifasController@masivos', 'as' => 'admin.tarifas.masivos']);
    Route::get('tarifas.costos', ['uses' => 'TarifasController@costos', 'as' => 'admin.tarifas.costos']);
    Route::post('tarifas/valor', ['as' => 'tarifas.valor', 'uses' => 'TarifasController@valor']);
    
    // Roles
    Route::get('roles/data', ['uses' => 'RolesController@anyData', 'as' => 'roles.data']);
    Route::resource('roles', 'RolesController');
    Route::get('roles/{id}/destroy', ['uses' => 'RolesController@destroy', 'as' => 'admin.roles.destroy']);
    
    //Perfiles
    Route::resource('perfiles', 'PerfilesController');
    Route::get('perfiles/{id}/destroy', ['uses' => 'PerfilesController@destroy', 'as' => 'admin.perfiles.destroy']);
    
    //Permisos
    Route::post('permisos/datos', ['as' => 'get.permisos', 'uses' => 'PermisosController@datos']);
    Route::post('permisos/asignar', ['as' => 'asignar.permisos', 'uses' => 'PermisosController@asignar']);
    Route::post('permisos/desasignar', ['as' => 'desasignar.permisos', 'uses' => 'PermisosController@desasignar']);
    

    // Estados
    Route::get('estados/data', ['uses' => 'EstadosController@anyData', 'as' => 'estados.data']);
    Route::resource('estados', 'EstadosController');
    Route::get('estados/{id}/destroy', ['uses' => 'EstadosController@destroy', 'as' => 'admin.estados.destroy']);
    // Zonas
    Route::get('zonas/{id}/destroy', ['uses' => 'ZonasController@destroy', 'as' => 'admin.zonas.destroy']);
    Route::get('zonas/data', ['uses' => 'ZonasController@anyData', 'as' => 'zonas.data']);
    Route::resource('zonas', 'ZonasController');
    // SubZonas
    Route::get('subzonas/{id}/data', ['uses' => 'SubzonasController@anyData', 'as' => 'subzonas.data']);
    Route::resource('subzonas', 'SubzonasController');
    Route::get('subzonas/{id}/destroy', ['uses' => 'SubzonasController@destroy', 'as' => 'admin.subzonas.destroy']);
    Route::get('subzonas/{id}/index', ['uses' => 'SubzonasController@index', 'as' => 'admin.subzonas.index']);
    // Limites
    Route::get('limites/{id}/data', ['uses' => 'LimitesController@anyData', 'as' => 'limites.data']);
    Route::resource('limites', 'LimitesController');
    Route::get('limites/{id}/destroy', ['uses' => 'LimitesController@destroy', 'as' => 'admin.limites.destroy']);
    Route::get('limites/{id}/index', ['uses' => 'LimitesController@index', 'as' => 'admin.limites.index']);
    // Ciudades
    Route::get('ciudades/data', ['uses' => 'CiudadesController@anyData', 'as' => 'ciudades.data']);
    Route::resource('ciudades', 'CiudadesController');
    Route::get('ciudades/{id}/destroy', ['uses' => 'CiudadesController@destroy', 'as' => 'admin.ciudades.destroy']);
    // Productos
    Route::get('productos/data', ['uses' => 'ProductosController@anyData', 'as' => 'productos.data']);
    Route::resource('productos', 'ProductosController');
    Route::get('productos/{id}/destroy', ['uses' => 'ProductosController@destroy', 'as' => 'admin.productos.destroy']);
    // Dominios
    Route::get('dominios/data', ['uses' => 'DominiosController@anyData', 'as' => 'dominios.data']);
    Route::resource('dominios', 'DominiosController');
    Route::get('dominios/{id}/destroy', ['uses' => 'DominiosController@destroy', 'as' => 'admin.dominios.destroy']);
    // Servicios
    Route::get('servicios/data', ['uses' => 'ServiciosController@anyData', 'as' => 'servicios.data']);
    Route::resource('servicios', 'ServiciosController');
    Route::get('servicios/{id}/destroy', ['uses' => 'ServiciosController@destroy', 'as' => 'admin.servicios.destroy']);
    // Sucursales
    Route::get('oficinas/data', ['uses' => 'OficinasController@anyData', 'as' => 'oficinas.data']);
    Route::resource('oficinas', 'OficinasController');
    Route::get('oficinas/{id}/destroy', ['uses' => 'OficinasController@destroy', 'as' => 'admin.oficinas.destroy']);
    //Resoluciones
    Route::get('resoluciones/data', ['uses' => 'ResolucionesController@anyData', 'as' => 'resoluciones.data']);
    Route::resource('resoluciones', 'ResolucionesController');
    Route::get('resoluciones/{id}/destroy', ['uses' => 'ResolucionesController@destroy', 'as' => 'admin.resoluciones.destroy']);
    //Log
    Route::resource('logs', 'LogsController');
    // Recogidas
    Route::post('recogidas/asignar3', ['uses' => 'RecogidasController@asignar3', 'as' => 'admin.recogidas.asignar3']);
    Route::post('datos_recogida', ['uses' => 'RecogidasController@datos', 'as' => 'admin.recogidas.datos']);
    Route::post('recogidas/ingresar_update', ['uses' => 'RecogidasController@ingresar_update', 'as' => 'admin.recogidas.ingresar_update']);
    Route::get('recogidas/asignar', ['uses' => 'RecogidasController@asignar', 'as' => 'admin.recogidas.asignar']);
    Route::post('recogidas/asignar2', ['uses' => 'RecogidasController@asignar2', 'as' => 'admin.recogidas.asignar2']);
    Route::get('recogidas/data', ['uses' => 'RecogidasController@anyData', 'as' => 'recogidas.data']);
    Route::get('recogidas/{id}/ingresar', ['uses' => 'RecogidasController@ingresar', 'as' => 'admin.recogidas.ingresar']);
    Route::get('recogidas/{id}/ingresar_pago', ['uses' => 'RecogidasController@ingresar_pago', 'as' => 'admin.recogidas.ingresar_pago']);
    Route::any('recogidas/calendario', ['uses' => 'RecogidasController@calendario', 'as' => 'admin.calendario.recogidas']);
    Route::resource('recogidas', 'RecogidasController', ['only' => ['index', 'create', 'store','edit','update']]);
    // Ordenes
    Route::get('ordenes/llegada/{id}/', ['uses' => 'OrdenesController@datosOrden', 'as' => 'admin.ordenes.llegada']);
    Route::get('ordenes/obs/{id}/', ['uses' => 'OrdenesController@datosObservaciones', 'as' => 'admin.ordenes.obs']);
    Route::post('ordenes/llegada/update/', ['uses' => 'OrdenesController@updateLlegada', 'as' => 'admin.ordenes.update.llegada']);
    Route::post('ordenes/impresion/update/', ['uses' => 'OrdenesController@updateImpresion', 'as' => 'admin.ordenes.update.impresion']);
    Route::post('ordenes/alistamiento/update/', ['uses' => 'OrdenesController@updateAlistamiento', 'as' => 'admin.ordenes.update.alistamiento']);
    Route::post('ordenes/clasificacion/update/', ['uses' => 'OrdenesController@updateClasificacion', 'as' => 'admin.ordenes.update.clasificacion']);
    Route::resource('ordenes', 'OrdenesController', ['only' => ['index', 'create', 'store']]);
    Route::any('ordenes/data', ['uses' => 'OrdenesController@anyData', 'as' => 'ordenes.data']);
    Route::any('ordenes/listar', ['uses' => 'OrdenesController@listaOrdenes', 'as' => 'ordenes.listar']);
    Route::get('ordenes/{id}/detalle_orden/', ['uses' => 'OrdenesController@detalle', 'as' => 'admin.ordenes.detalle']);
    //Opciones de orden
    Route::get('con_recogida', ['as' => 'admin.ordenes.numero', 'uses' => 'OrdenesController@conRecogida']);
    Route::get('sin_recogida', ['as' => 'admin.ordenes.sinnumero', 'uses' => 'OrdenesController@sinNumero']);
    //Para procesar los planos
    Route::post('uploads', ['as' => 'uploads', 'uses' => 'FilesController@Uploads_init']);
    Route::post('uploads_init', ['as' => 'uploads_init', 'uses' => 'FilesController@postUploads']);
    Route::resource('files', 'FilesController');
    //Para las envios
    Route::any('envios/resultados', ['uses' => 'EnviosController@resultados', 'as' => 'admin.envios.resultados']);
    Route::resource('envios', 'EnviosController');
    Route::get('envios/{id}/detalle/', ['uses' => 'EnviosController@detalle', 'as' => 'admin.envios.detalle']);
    
    //Para los reportes
    Route::any('reportes/datos', ['uses' => 'ReportesController@anyData', 'as' => 'admin.reportes.datos']);
    Route::any('reportes/datos/orders', ['uses' => 'ReportesController@ordersData', 'as' => 'admin.reportes.datos.orders']);
    Route::any('reportes/descargar', ['uses' => 'ReportesController@descargar', 'as' => 'admin.reportes.descargar']);
    Route::resource('reportes', 'ReportesController', ['only' => ['index']]);
    //Manifiestos
    Route::get('manifiestos/datos/{id}/', ['uses' => 'ManifiestosController@datos', 'as' => 'admin.manifiestos.datos']);
    Route::any('manifiestos/descargar', ['uses' => 'ManifiestosController@descargar', 'as' => 'admin.manifiestos.descargar']);
    Route::resource('manifiestos', 'ManifiestosController', ['only' => ['index', 'create', 'store']]);
    Route::any('manifiestos/data', ['uses' => 'ManifiestosController@anyData', 'as' => 'manifiestos.data']);
    Route::get('manifiestos/{id}/destroy', ['uses' => 'ManifiestosController@destroy', 'as' => 'admin.manifiestos.destroy']);
    //Punteo
    Route::any('punteo.subirarchivo', ['uses' => 'PunteoController@subirArchivo', 'as' => 'punteo.subirarchivo']);
    Route::any('firmar', ['uses' => 'PunteoController@firmar', 'as' => 'firmar']);
    Route::get('punteo', ['uses' => 'PunteoController@punteo', 'as' => 'admin.punteo.index']);
    Route::get('punteo/inconsistencias/{id}/', ['uses' => 'PunteoController@datos', 'as' => 'admin.punteo.inconsistencias']);
    //Digitacion
    Route::get('digitacion/cuenta/{id}', ['uses' => 'DigitacionController@cuenta', 'as' => 'admin.digitacion.cuenta']);
    Route::resource('digitacion', 'DigitacionController', ['only' => ['index', 'store']]);
    //Facturacion
    Route::any('factura_imprimir/{id}/email/{email}', ['uses' => 'FacturacionController@imprimir', 'as' => 'admin.facturacion.imprimir']);
    Route::any('facturacion/buscar', ['uses' => 'FacturacionController@buscar', 'as' => 'admin.facturacion.buscar']);
    Route::any('facturacion/data', ['uses' => 'FacturacionController@anyData', 'as' => 'facturas.data']);
    Route::resource('facturacion', 'FacturacionController', ['only' => ['index', 'store', 'create']]);
    //Terceros
    Route::get('terceros', ['uses' => 'TercerosController@index', 'as' => 'admin.terceros.index']);
    Route::get('terceros/data', ['uses' => 'TercerosController@anyData', 'as' => 'admin.terceros.data']);
    Route::get('terceros/{id}', ['uses' => 'TercerosController@show', 'as' => 'admin.terceros.show']);
    Route::get('terceros/{id}/edit', ['uses' => 'TercerosController@edit', 'as' => 'admin.terceros.edit']);
    Route::put('terceros/{id}', ['uses' => 'TercerosController@update', 'as' => 'admin.terceros.update']);
});