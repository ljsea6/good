<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Entities\Tercero;

class Tipo extends Model {

	use SoftDeletes;

	protected $table = 'tipos';

	protected $fillable = ['id','nombre','padre_id'];

	protected $dates = ['deleted_at'];


	public function padre() {
        return $this->belongsTo('App\Entities\Tipo','padre_id','id');
    }

 public function scopeTipoidenti($query, $type) {
        return $query->select('tipos.id')->leftjoin('terceros as d','d.tipo_id','=','tipos.id')->where('tipos.id', $type);
                   // return $query->
                
              }
    
    public function scopeTipoIdentificacion($query) {
        return $query->select('nombre','id')->whereHas('padre', function ($query) {
                    $query->where('nombre','Tipo Identificacion');
                });
            // return $query->where('nombre', $type);
    }



    public function scopeTipoProducto($query) {
        return $query->select('nombre','id')->whereHas('padre', function ($query) {
                    $query->where('nombre','Tipo Producto');
                });
            // return $query->where('nombre', $type);
    }

    public function scopeTipoMensajeria($query) {
        return $query->select('nombre','id')->whereHas('padre', function ($query) {
                    $query->where('nombre','Tipo Mensajeria');
                });
            // return $query->where('nombre', $type);
    }

    public function scopeTipoDestino($query) {
        return $query->select('nombre','id')->whereHas('padre', function ($query) {
    				$query->where('nombre','Tipo Destino');
				});
            // return $query->where('nombre', $type);
    }
    
    public function scopeTipoTiempoEntrega($query) {
        return $query->select('nombre','id')->whereHas('padre', function ($query) {
                    $query->where('nombre','Tipo Tiempo Entrega');
                });
            // return $query->where('nombre', $type);
    }
    public function scopeTipoPago($query) {
        return $query->select('nombre','id')->whereHas('padre', function ($query) {
                    $query->where('nombre','Tipo Pago');
                });
            // return $query->where('nombre', $type);
    }
    public function scopeTipoManifiesto($query) {
        return $query->select('nombre','id')->whereHas('padre', function ($query) {
                    $query->where('nombre','Tipo Manifiesto');
                });
            // return $query->where('nombre', $type);
    }
    public function scopeTipoMensajero($query) {
        return $query->select('nombre','id')->whereHas('padre', function ($query) {
                    $query->where('nombre','Tipo Mensajero');
                });
            // return $query->where('nombre', $type);
    }
    public function scopeTipoUsuario($query) {
        return $query->where('nombre', 'Proveedor');
    }
}
