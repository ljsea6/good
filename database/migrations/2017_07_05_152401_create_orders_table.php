<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('billing_address')->nullable();
            $table->string('browser_ip')->nullable();
            $table->boolean('buyer_accepts_marketing')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cart_token')->nullable();
            $table->json('client_details')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('currency')->nullable();
            $table->bigInteger('customer_id');
            $table->json('discount_codes')->nullable();
            $table->string('email')->nullable();
            $table->string('financial_status')->nullable();
            $table->json('fulfillments')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->string('tags')->nullable();
            $table->string('gateway')->nullable();
            $table->string('landing_site')->nullable();
            $table->string('landing_site_ref')->nullable();
            $table->json('line_sites')->nullable();
            $table->double('location_id')->nullable();
            $table->string('name')->nullable();
            $table->integer('network_id');
            $table->string('note')->nullable();
            $table->json('note_attributes')->nullable();
            $table->integer('number')->nullable();
            $table->bigInteger('order_id');
            $table->bigInteger('order_number')->nullable();
            $table->json('payment_details')->nullable();
            $table->json('payment_gateway_names')->nullable();
            $table->string('phone')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('processing_method')->nullable();
            $table->string('referring_site')->nullable();
            $table->json('line_items')->nullable();
            $table->json('refunds')->nullable();
            $table->json('shipping_address')->nullable();
            $table->json('shipping_lines')->nullable();
            $table->string('source_name')->nullable();
            $table->double('subtotal_price')->nullable();
            $table->json('tax_lines')->nullable();
            $table->double('taxes_included')->nullable();
            $table->string('token')->nullable();
            $table->double('total_discounts')->nullable();
            $table->double('total_line_items_price')->nullable();
            $table->double('total_price')->nullable();
            $table->double('total_tax')->nullable();
            $table->integer('total_weight')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('order_status_url')->nullable();
            $table->timestamps();
            $table->boolean('test')->nullable();
            $table->double('total_max')->nullable();
            $table->boolean('confirmed')->nullable();
            $table->double('total_price_usd')->nullable();
            $table->string('checkout_token')->nullable();
            $table->double('reference')->nullable();
            $table->string('source_identifier')->nullable();
            $table->string('source_url')->nullable();
            $table->double('device_id')->nullable();
            $table->bigInteger('checkout_id')->nullable();
            $table->string('contact_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
