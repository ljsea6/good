<?php
namespace App\Entities;

use Bican\Roles\Traits\HasRoleAndPermission;
use Bican\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Tercero extends Model implements AuthenticatableContract, CanResetPasswordContract, HasRoleAndPermissionContract {
    use Authenticatable, CanResetPassword, HasRoleAndPermission;

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'terceros';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'apellidos',
        'cargo_id',
        'ciudad_id',
        'avatar',
        'contraseña',
        'direccion',
        'email',
        'tipo_identificacion_id',
        'identificacion',
        'nombres',
        'resolucion_id',
        'sector_id',
        'sub_zona_id',
        'sucursal_id',
        'telefono',
        'celular',
        'usuario',
        'zona_id',
        'padre_id',
        'control_ip',
        'ips_autorizadas',
        'tipo_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'contraseña',
        'remember_token',
    ];

    public function getAuthPassword() {
        return $this->contraseña;
    }

    public function getId() {
        return $this->id;
    }

    public function cargo() {
        return $this->belongsTo('App\Entities\Cargo');
    }

    public function ciudad() {
        return $this->belongsTo('App\Entities\Ciudad');
    }

    public function resolucion() {
        return $this->belongsTo('App\Entities\Resolucion');
    }

    public function sector() {
        return $this->belongsTo('App\Entities\Sector');
    }

    public function sucursal() {
        return $this->belongsTo('App\Entities\Sucursal');
    }

    public function tipo() {
        return $this->belongsTo('App\Entities\Tipo');
    }

    public function zona() {
        return $this->belongsTo('App\Entities\Zona');
    }

    public function scopeTipoUsuario($query, $type) {
        return $query->where('tipo_id', $type);
    }

    public function getNombreCompletoAttribute() {
        return $this->nombres;
    }

    public function orders()
    {
        return $this->belongsToMany(Tercero::class, 'orders', 'customer_id', 'network_id');
    }

    public function networks()
    {
        return $this->belongsToMany(Network::class, 'terceros_networks', 'customer_id', 'network_id')->withPivot('network_id', 'padre_id')->withTimestamps();
    }

}
