<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            
            $table->enum('transaction_type', ['income','expense']);
            
            $table->integer('refID')->nullable();//will keep sales i.e invoice id or payment as per transaction type initiated
            
            $table->decimal('amount');

            $table->string('transaction_name')->nullable();
            
            $table->text('date');

            $table->text('image')->nullable();

            $table->unsignedBigInteger('store_id');

            $table->text('notes')->nullable();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        
            $table->unsignedBigInteger('account_id');
            
            $table->foreign('account_Id')->references('id')->on('accounts')->onDelete('cascade');

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
        Schema::dropIfExists('transactions');
    }
}
