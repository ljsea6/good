<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model {

	use SoftDeletes;

	protected $table = 'logs';

	//protected $dates = ['deleted_at'];

	//protected $fillable = ['codigo_dane', 'nombre', 'departamento', 'region','tipo_id'];
}
