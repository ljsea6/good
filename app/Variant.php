<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;

class Variant extends Model
{
    protected $table = 'variants';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
