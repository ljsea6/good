<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ciudad extends Model {

	use SoftDeletes;

	protected $table = 'ciudades';

	protected $dates = ['deleted_at'];

	protected $fillable = ['codigo_dane', 'nombre', 'departamento', 'region', 'tipo_id', 'usuario_id', 'ip'];

	public function getNombreCompletoAttribute() {
		return $this->departamento . ' / ' . $this->nombre;
	}
}
