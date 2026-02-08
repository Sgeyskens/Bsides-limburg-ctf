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
        Schema::create('discount_code', function (Blueprint $table) {
            $table->id('code_id');
            $table->string('code')->unique();
            $table->string('discount_percentage');
            $table->decimal('discount_amount', 10, 2);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->integer('max_uses');
            $table->integer('current_uses')->default(0);
            $table->string('applies_to')->nullable();


            $table->decimal('minimum_purchase', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_code');
    }
};