<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id',      'idx_orders_user_id');
            $table->index('status',       'idx_orders_status');
            $table->index('created_at',   'idx_orders_created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id',  'idx_products_category_id');
            $table->index('brand_id',     'idx_products_brand_id');
            $table->index('is_active',    'idx_products_is_active');
            $table->index(['is_active', 'stock_quantity'], 'idx_products_active_stock');
        });

        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->index('user_id',      'idx_loyalty_tx_user_id');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->index('user_id',      'idx_wishlists_user_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('product_id',   'idx_reviews_product_id');
            $table->index('is_approved',  'idx_reviews_is_approved');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user_id');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_category_id');
            $table->dropIndex('idx_products_brand_id');
            $table->dropIndex('idx_products_is_active');
            $table->dropIndex('idx_products_active_stock');
        });

        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_loyalty_tx_user_id');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex('idx_wishlists_user_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_product_id');
            $table->dropIndex('idx_reviews_is_approved');
        });
    }
};
