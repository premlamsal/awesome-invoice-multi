<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('custom_return_purchase_id')->nullable();
            $table->date('return_purchase_date');
            $table->date('due_date');
            $table->string('image')->nullable();
            $table->string('note');
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->string('supplier_name');
            $table->string('sub_total');
            $table->string('return_purchase_reference_id');
            $table->decimal('discount');
            $table->decimal('tax_amount');
            $table->decimal('grand_total');
            $table->string('status');
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
        Schema::dropIfExists('return_purchases');
    }
}
