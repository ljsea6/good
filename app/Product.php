<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $casts = [
        'image' => 'array',
        'images' => 'array',
        'variants' => 'array',
        'options' => 'array',
    ];
    protected $guarded = [];
}
