<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resolucion extends Model {

	use SoftDeletes;

	protected $table = 'resoluciones';

	protected $fillable = ['numero','prefijo','digitos','nombre','desde','hasta','vencimiento'];

	protected $dates = ['deleted_at'];
}
