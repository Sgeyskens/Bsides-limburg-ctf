<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes to improve filter and sort performance on large datasets.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Index for product type filtering
            $table->index('product_type', 'idx_products_type');

            // Index for price sorting/filtering
            $table->index('price', 'idx_products_price');

            // Composite index for type + price (common filter combination)
            $table->index(['product_type', 'price'], 'idx_products_type_price');

            // Index for name sorting
            $table->index('name', 'idx_products_name');
        });

        Schema::table('product_property', function (Blueprint $table) {
            // Index for property value filtering
            $table->index('property_value', 'idx_product_property_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_type');
            $table->dropIndex('idx_products_price');
            $table->dropIndex('idx_products_type_price');
            $table->dropIndex('idx_products_name');
        });

        Schema::table('product_property', function (Blueprint $table) {
            $table->dropIndex('idx_product_property_value');
        });
    }
};
