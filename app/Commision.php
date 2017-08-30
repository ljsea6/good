<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commision extends Model
{
    protected $table = 'commisions';
    protected $dates = ['created_at'];

    protected $casts = [
        'gift_card' => 'array',
        'orders' => 'array',
        'bitacora' => 'array'
    ];

    protected $guarded = [];
}
