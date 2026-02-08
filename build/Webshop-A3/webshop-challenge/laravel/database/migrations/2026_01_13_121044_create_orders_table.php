<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('order_date');
            $table->integer('total_amount');
            $table->string('status'); // pending, processing, shipped, delivered, cancelled
            $table->text('shipping_address');
            $table->text('billing_address');
            $table->string('tracking_number')->nullable();
            $table->string('discount_code')->nullable();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};