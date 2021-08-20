<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            
            $table->string('custom_product_id')->nullable();

            $table->string('name');
            
            $table->string('image');
            
            $table->string('description');

            $table->decimal('cp');

            $table->decimal('sp');
            
            $table->string('opening_stock');
            
            $table->unsignedBigInteger('product_cat_id');
            
            $table->foreign('product_cat_id')->references('id')->on('product_categories')->onDelete('cascade');
 
            $table->unsignedBigInteger('unit_id');
            
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->unsignedBigInteger('store_id');
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

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
        Schema::dropIfExists('products');
    }
}
