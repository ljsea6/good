<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('body_html')->nullable();
            $table->string('handle')->nullable();
            $table->json('image')->nullable();
            $table->json('images')->nullable();
            $table->json('options')->nullable();
            $table->string('product_type')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('published_scope')->nullable();
            $table->string('tags')->nullable();
            $table->string('template_suffix')->nullable();
            $table->text('title')->nullable();
            $table->string('metafields_global_title_tag')->nullable();
            $table->string('metafields_global_description_tag')->nullable();
            $table->json('variants')->nullable();
            $table->string('vendor')->nullable();
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
        Schema::drop('products');
    }
}
