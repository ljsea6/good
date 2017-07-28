<?php
namespace App\Entities;




class Archivo extends Model {
   

 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'archivos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'fecha',
        'usuario_id',
        'nombre',
        'tamaño',
        'tipo_ext',
        'tipo_id',
        'estado',
        'deleted_at',
        'created_at',
        'ip'
    ];
    
}
