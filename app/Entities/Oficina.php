<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Oficina extends Model {

	use SoftDeletes;

	protected $table = 'oficinas';

	protected $fillable = ['nombre','direccion','telefono','email','ciudad_id'];

	protected $dates = ['deleted_at'];

	public function ciudad() {
		return $this->belongsTo('App\Entities\Ciudad','ciudad_id','id');
	}	
}
