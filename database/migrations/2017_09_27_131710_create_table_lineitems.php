<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLineitems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lineitems', function (Blueprint $table) {
            $table->bigInteger('id')->nullable();
            $table->bigInteger('variant_id')->nullable();
            $table->string('title')->nullable();
            $table->integer('quantity')->nullable();
            $table->double('price')->nullable();
            $table->string('grams')->nullable();
            $table->string('sku')->nullable();
            $table->string('variant_title')->nullable();
            $table->string('vendor')->nullable();
            $table->string('fulfillment_service')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->boolean('requires_shipping')->nullable();
            $table->boolean('taxable')->nullable();
            $table->boolean('gift_card')->nullable();
            $table->double('pre_tax_price')->nullable();
            $table->string('name')->nullable();
            $table->string('variant_inventory_management')->nullable();
            $table->json('properties')->nullable();
            $table->boolean('product_exists')->nullable();
            $table->integer('fulfillable_quantity')->nullable();
            $table->double('total_discount')->nullable();
            $table->string('fulfillment_status')->nullable()->default(null);
            $table->json('tax_lines')->nullable();
            $table->json('origin_location')->nullable();
            $table->json('destination_location')->nullable();
            $table->string('order_name');
            $table->bigInteger('order_id')->nullable();
            $table->timestamp('date_order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lineitems');
    }
}
