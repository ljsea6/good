<?php
//use Tzsk\Payu\Fragment\Payable;
//use Alexo\LaravelPayU\Payable;
//use Alexo\LaravelPayU\Searchable;
use Illuminate\Database\Eloquent\Model;


class Order extends Model {
    use Payable;
    
     protected $fillable = [
        'reference', 'payu_order_id',  'transaction_id', 'state', 'value', 'user_id'
    ];
}
//# That's it. Same applies to Any number of Models that you feel accept payments.