<?php
namespace App\Entities;


use Illuminate\Database\Eloquent\Model;
use App\Entities\Tercero;

class Tercero_network extends Model {


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'terceros_networks';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = [];

}
