<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Comision_detalle extends Model{
    

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comisiones_detalles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_comision',
        'id_tercero',
        'id_transaccion',
        'id_regla',
        'valor',
        'estado',
    ];

    protected $dates = ['deleted_at'];
    
 }