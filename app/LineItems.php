<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LineItems extends Model
{
    protected $table = 'lineitems';

    protected $casts = [
        'properties' => 'array',
        'tax_lines' => 'array',
        'origin_location' => 'array',
        'destination_location' => 'array'
    ];

    protected $guarded = [];
}
