<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('accepts_marketing')->nullable();
            $table->json('addresses')->nullable();
            $table->bigInteger('customer_id');
            $table->json('default_address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('first_name')->nullable();
            $table->json('metafield')->nullable();
            $table->string('multipass_identifier')->nullable();
            $table->integer('network_id');
            $table->string('last_name')->nullable();
            $table->bigInteger('last_order_id')->nullable();
            $table->string('last_order_name')->nullable();
            $table->string('note')->nullable();
            $table->bigInteger('orders_count')->nullable();
            $table->string('state')->nullable();
            $table->string('tags')->nullable();
            $table->boolean('tax_exempt')->nullable();
            $table->double('total_spent')->nullable();
            $table->boolean('verified_email')->nullable();
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
        Schema::drop('customers');
    }
}
