<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - Add critical database indexes for performance optimization
     */
    public function up(): void
    {
        // Enable timing for index creation
        DB::statement('SET statement_timeout = 300000'); // 5 minutes timeout

        // Foreign Key Indexes (Critical for JOIN performance)
        Schema::table('members', function (Blueprint $table) {
            $table->index('user_id', 'idx_members_user_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('product_id', 'idx_order_items_product_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('product_id', 'idx_reviews_product_id');
            $table->index('member_id', 'idx_reviews_member_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index('product_id', 'idx_cart_items_product_id');
            $table->index('member_id', 'idx_cart_items_member_id');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->index('product_id', 'idx_wishlists_product_id');
            $table->index('member_id', 'idx_wishlists_member_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('order_id', 'idx_payments_order_id');
        });

        Schema::table('shipping', function (Blueprint $table) {
            $table->index('order_id', 'idx_shipping_order_id');
        });

        // Composite Indexes for Complex Queries
        Schema::table('products', function (Blueprint $table) {
            // For category + brand filtering
            $table->index(['category_id', 'brand_id'], 'idx_products_category_brand');

            // For active products with price and stock filtering
            $table->index(['status', 'price', 'stock_quantity'], 'idx_products_status_price_stock');

            // For sorting by popularity and date
            $table->index(['view_count', 'created_at'], 'idx_products_popularity_date');
        });

        Schema::table('orders', function (Blueprint $table) {
            // For user order history with status filtering
            $table->index(['member_id', 'order_status', 'created_at'], 'idx_orders_member_status_date');

            // For payment status queries
            $table->index(['order_status', 'payment_status'], 'idx_orders_status_payment');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // For order details queries
            $table->index(['order_id', 'product_id'], 'idx_order_items_order_product');
        });

        // Full-text Search Indexes (PostgreSQL specific)
        // Trigram indexes for similarity search
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_products_name_trgm ON products USING gin(product_name gin_trgm_ops)');
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_products_description_trgm ON products USING gin(COALESCE(description, \'\') gin_trgm_ops)');

        // GIN index for JSON specifications search
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_products_specifications ON products USING gin(specifications)');

        // Partial Indexes for Active Records Only
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_products_active_only ON products(product_id) WHERE status = \'active\'');
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_products_in_stock ON products(product_id) WHERE stock_quantity > 0');
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_orders_pending_only ON orders(order_id) WHERE order_status = \'pending\'');
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_orders_processing ON orders(order_id) WHERE order_status IN (\'processing\', \'shipped\')');

        // Analytics and Reporting Indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['created_at', 'order_status'], 'idx_orders_date_status');
            $table->index(['created_at', 'total_amount'], 'idx_orders_date_amount');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['rating', 'helpful_count', 'created_at'], 'idx_reviews_rating_helpful_date');
        });

        // User search optimization
        Schema::table('users', function (Blueprint $table) {
            $table->index(['is_active', 'last_login'], 'idx_users_active_login');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->index(['first_name', 'last_name'], 'idx_members_name');
            $table->index('province', 'idx_members_province');
        });

        // Reset timeout
        DB::statement('RESET statement_timeout');
    }

    /**
     * Reverse the migrations - Remove all added indexes
     */
    public function down(): void
    {
        // Foreign Key Indexes
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('idx_members_user_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_product_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_product_id');
            $table->dropIndex('idx_reviews_member_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('idx_cart_items_product_id');
            $table->dropIndex('idx_cart_items_member_id');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex('idx_wishlists_product_id');
            $table->dropIndex('idx_wishlists_member_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_order_id');
        });

        Schema::table('shipping', function (Blueprint $table) {
            $table->dropIndex('idx_shipping_order_id');
        });

        // Composite Indexes
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_category_brand');
            $table->dropIndex('idx_products_status_price_stock');
            $table->dropIndex('idx_products_popularity_date');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_member_status_date');
            $table->dropIndex('idx_orders_status_payment');
            $table->dropIndex('idx_orders_date_status');
            $table->dropIndex('idx_orders_date_amount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_product');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_rating_helpful_date');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_active_login');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('idx_members_name');
            $table->dropIndex('idx_members_province');
        });

        // Drop GIN indexes
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_products_name_trgm');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_products_description_trgm');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_products_specifications');

        // Drop partial indexes
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_products_active_only');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_products_in_stock');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_orders_pending_only');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_orders_processing');
    }
};