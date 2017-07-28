<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model {

	protected $table = 'facturas';

	protected $fillable = [
		'numero',
		'valor',
		'iva',
		'descuento',
		'resolucion_id',
		'usuario_id',
		'cliente_id',
		'recogida_id',
		'ciudad_id',
	];

	public function resolucion() {
		return $this->belongsTo('App\Entities\Resolucion');
	}

	public function usuario() {
		return $this->belongsTo('App\Entities\Tercero',  'usuario_id', 'id');
	}

	public function cliente() {
		return $this->belongsTo('App\Entities\Tercero',  'cliente_id', 'id');
	}

	public function ciudad() {
		return $this->belongsTo('App\Entities\Ciudad',  'ciudad_id', 'id');
	}

	public function recogida() {
		return $this->belongsTo('App\Entities\Recogida');
	}

}
