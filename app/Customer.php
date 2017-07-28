<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Network;

class Customer extends Model
{
    protected $table = 'customers';

    protected $dates = ['created_at'];

    protected $casts = [
        'addresses' => 'array',
        'default_address' => 'array',
        'metafield' => 'array'
    ];

    protected $guarded = [];

    public function networks()
    {
        return $this->belongsToMany(Network::class, 'terceros_networks', 'customer_id', 'network_id')->withPivot('network_id', 'padre_id', 'state')->withTimestamps();
    }
}
