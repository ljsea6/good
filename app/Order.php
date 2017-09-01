<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Tercero;
use App\Entities\Network;

class Order extends Model
{
    protected $table = 'orders';

    protected $casts = [
        'billing_address' => 'array',
        'client_details' => 'array',
        'customer' => 'array',
        'discount_codes' => 'array',
        'fulfillments' => 'array',
        'line_items' => 'array',
        'note_attributes' => 'array',
        'payment_details' => 'array',
        'payment_gateway_names' => 'array',
        'shipping_address' => 'array',
        'shipping_lines' => 'array',
        'tax_lines' => 'array',
        'refunds' => 'array',
        'bitacora' => 'array'
    ];

    protected $guarded = [];

    public function tercero()
    {
        return $this->belongsTo(Tercero::class, 'customer_id', 'id');
    }

    public function network()
    {
        return $this->belongsTo(Network::class, 'network_id', 'id');
    }
}
