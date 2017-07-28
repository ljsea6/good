<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model {

	use SoftDeletes;

	protected $table = 'productos';

	protected $dates = ['deleted_at'];

	protected $fillable = ['nombre', 'descripcion', 'tipo_id'];

	public function tipo() {
		return $this->belongsTo('App\Entities\Tipo');
	}
}
