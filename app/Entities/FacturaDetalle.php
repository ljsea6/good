<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model {

	protected $table = 'facturas_detalle';

	protected $fillable = [
		'cantidad',
		'valor',
		'factura_id',
		'tipo_servicio_id',
		'tipo_tiempo_id',
	];

	public function factura() {
		return $this->belongsTo('App\Entities\Factura');
	}

	public function tipo_servicio() {
		return $this->belongsTo('App\Entities\Tipo', 'id', 'tipo_servicio_id');
	}

	public function tipo_tiempo() {
		return $this->belongsTo('App\Entities\Tipo', 'id', 'tipo_tiempo_id');
	}

}
