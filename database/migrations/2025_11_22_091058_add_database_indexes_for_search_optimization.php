<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add DSA-optimized database indexes
     */
    public function up(): void
    {
        // Products table indexes for efficient search and filtering
        Schema::table('products', function (Blueprint $table) {
            // Composite index for status and stock (most common filter)
            $table->index(['status', 'stock_quantity'], 'idx_products_status_stock');

            // Index for category filtering
            $table->index('category_id', 'idx_products_category');

            // Index for brand filtering
            $table->index('brand_id', 'idx_products_brand');

            // Index for price range queries (efficient for BETWEEN operations)
            $table->index('price', 'idx_products_price');

            // Index for sorting by creation date
            $table->index('created_at', 'idx_products_created_at');

            // Index for view count sorting (popularity)
            $table->index('view_count', 'idx_products_view_count');

            // Composite index for active products with price (common query pattern)
            $table->index(['status', 'price'], 'idx_products_status_price');

            // Full-text search index for product names (GIN index for PostgreSQL)
            $table->rawIndex("(to_tsvector('english', product_name || ' ' || coalesce(description, '')))", 'idx_products_fulltext_search');
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            // Index for category name searches
            $table->index('category_name', 'idx_categories_name');
        });

        // Brands table indexes
        Schema::table('brands', function (Blueprint $table) {
            // Index for brand name searches
            $table->index('brand_name', 'idx_brands_name');
        });

        // Users table indexes for authentication optimization
        Schema::table('users', function (Blueprint $table) {
            // Index for username login
            $table->index('username', 'idx_users_username');

            // Index for email login
            $table->index('email', 'idx_users_email');

            // Index for role-based queries
            $table->index('role_id', 'idx_users_role');
        });

        // Orders table indexes for efficient queries
        Schema::table('orders', function (Blueprint $table) {
            // Index for user orders lookup
            $table->index('user_id', 'idx_orders_user');

            // Index for order status filtering
            $table->index('status', 'idx_orders_status');

            // Index for date-based queries
            $table->index('created_at', 'idx_orders_created_at');

            // Composite index for user orders by date
            $table->index(['user_id', 'created_at'], 'idx_orders_user_date');
        });

        // Payments table indexes
        Schema::table('payments', function (Blueprint $table) {
            // Index for order payment lookup
            $table->index('order_id', 'idx_payments_order');

            // Index for payment status queries
            $table->index('status', 'idx_payments_status');

            // Index for payment method analytics
            $table->index('payment_method', 'idx_payments_method');
        });
    }

    /**
     * Reverse the migrations - Remove all added indexes
     */
    public function down(): void
    {
        // Remove products table indexes
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status_stock');
            $table->dropIndex('idx_products_category');
            $table->dropIndex('idx_products_brand');
            $table->dropIndex('idx_products_price');
            $table->dropIndex('idx_products_created_at');
            $table->dropIndex('idx_products_view_count');
            $table->dropIndex('idx_products_status_price');
            $table->dropIndex('idx_products_fulltext_search');
        });

        // Remove categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_name');
        });

        // Remove brands table indexes
        Schema::table('brands', function (Blueprint $table) {
            $table->dropIndex('idx_brands_name');
        });

        // Remove users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_username');
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_role');
        });

        // Remove orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_user_date');
        });

        // Remove payments table indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_order');
            $table->dropIndex('idx_payments_status');
            $table->dropIndex('idx_payments_method');
        });
    }
};
