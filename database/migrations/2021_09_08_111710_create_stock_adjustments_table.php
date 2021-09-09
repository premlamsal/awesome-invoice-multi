<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('product_id');
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->unsignedBigInteger('stock_id');
            
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');

            $table->date('date');

            $table->string('reason');

            $table->string('type');

            $table->text('notes')->nullable();
            
            $table->decimal('quantity');

            $table->decimal('price');

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
        Schema::dropIfExists('stock_adjustments');
    }
}
