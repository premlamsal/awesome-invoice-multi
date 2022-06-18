<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->text('detail')->nullable();
            $table->string('mobile');
            $table->string('email')->unique();
            $table->string('url')->nullable();
            $table->string('tax_number');
            $table->decimal('tax_percentage');
            $table->decimal('profit_percentage');
            $table->string('product_id_count')->default('PRO-0');
            $table->string('invoice_id_count')->default('INV-0');
            $table->string('purchase_id_count')->default('PUR-0');
            $table->string('return_invoice_id_count')->default('RINV-0');
            $table->string('return_purchase_id_count')->default('RPUR-0');
            $table->string('store_logo')->nullable();
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
        Schema::dropIfExists('stores');
    }
}
