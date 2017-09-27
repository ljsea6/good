<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Variant;

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

    public function variants_product()
    {
        return $this->hasMany(Variant::class, 'product_id', 'id');
    }
}
