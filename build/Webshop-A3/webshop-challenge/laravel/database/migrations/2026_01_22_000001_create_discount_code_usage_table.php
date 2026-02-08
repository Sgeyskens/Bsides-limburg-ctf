<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table tracks which user has used each discount code.
     * Each user can use a code once, but different users can all use the same code.
     * The race condition for same-user discount stacking still works because
     * the claim check happens BEFORE the sleep window.
     */
    public function up(): void
    {
        Schema::create('discount_code_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('code_id');
            $table->timestamp('applied_at');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('code_id')->references('code_id')->on('discount_code')->onDelete('cascade');

            // Unique constraint on user_id + code_id: each user can use a code once
            // Different users can all use the same code
            $table->unique(['user_id', 'code_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_code_usage');
    }
};
