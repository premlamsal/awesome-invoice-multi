<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnPurchaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_purchase_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('return_purchase_id');
            $table->foreign('return_purchase_id')->references('id')->on('return_purchases')->onDelete('cascade');

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->string('product_name');
            $table->string('quantity');
            $table->string('price');
            
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->string('line_total');
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
        Schema::dropIfExists('return_purchase_details');
    }
}
