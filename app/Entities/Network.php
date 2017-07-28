<?php
namespace App\Entities;

use App\Entities\Tercero;
use App\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Network extends Model{
    

    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'networks';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'frontal_amount',
        'depth'
    ];

    protected $dates = ['deleted_at'];

    public function terceros()
    {
        return $this->belongsToMany(Tercero::class, 'terceros_networks', 'network_id', 'customer_id')->withPivot('customer_id', 'padre_id')->withTimestamps();
    }


    public function orders()
    {
        return $this->hasMany(Order::class, 'network_id', 'id');
    }

 }