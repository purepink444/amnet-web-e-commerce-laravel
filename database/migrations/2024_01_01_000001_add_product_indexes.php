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
        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            // Search performance indexes
            $table->index(['product_name'], 'idx_products_name');
            $table->index(['status'], 'idx_products_status');
            $table->index(['category_id'], 'idx_products_category');
            $table->index(['brand_id'], 'idx_products_brand');
            $table->index(['views'], 'idx_products_views');

            // Composite indexes for common queries
            $table->index(['status', 'category_id'], 'idx_products_status_category');
            $table->index(['status', 'brand_id'], 'idx_products_status_brand');
            $table->index(['category_id', 'views'], 'idx_products_category_views');
            $table->index(['price'], 'idx_products_price');
            $table->index(['created_at'], 'idx_products_created_at');
            $table->index(['updated_at'], 'idx_products_updated_at');

            // Full-text search index (PostgreSQL specific)
            if (Schema::getConnection()->getDriverName() === 'pgsql') {
                $table->rawIndex(
                    "(to_tsvector('english', coalesce(product_name, '') || ' ' || coalesce(description, '')))",
                    'idx_products_fts'
                );
            }

            // Partial indexes for active products
            $table->index(['status'], 'idx_products_active_only')->where('status', 'active');
            $table->index(['status', 'stock_quantity'], 'idx_products_active_in_stock')
                  ->where('status', 'active')
                  ->where('stock_quantity', '>', 0);
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['category_name'], 'idx_categories_name');
            $table->index(['status'], 'idx_categories_status');
            $table->index(['parent_id'], 'idx_categories_parent');
            $table->index(['created_at'], 'idx_categories_created_at');
        });

        // Brands table indexes
        Schema::table('brands', function (Blueprint $table) {
            $table->index(['brand_name'], 'idx_brands_name');
            $table->index(['status'], 'idx_brands_status');
            $table->index(['created_at'], 'idx_brands_created_at');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index(['email'], 'idx_users_email');
            $table->index(['username'], 'idx_users_username');
            $table->index(['role_id'], 'idx_users_role');
            $table->index(['created_at'], 'idx_users_created_at');
        });

        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id'], 'idx_orders_user');
            $table->index(['order_status'], 'idx_orders_status');
            $table->index(['created_at'], 'idx_orders_created_at');
            $table->index(['updated_at'], 'idx_orders_updated_at');
            $table->index(['user_id', 'order_status'], 'idx_orders_user_status');
            $table->index(['order_status', 'created_at'], 'idx_orders_status_date');
        });

        // Order items table indexes
        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['order_id'], 'idx_order_items_order');
            $table->index(['product_id'], 'idx_order_items_product');
            $table->index(['order_id', 'product_id'], 'idx_order_items_order_product');
        });

        // Reviews table indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['product_id'], 'idx_reviews_product');
            $table->index(['member_id'], 'idx_reviews_member');
            $table->index(['rating'], 'idx_reviews_rating');
            $table->index(['created_at'], 'idx_reviews_created_at');
            $table->index(['product_id', 'rating'], 'idx_reviews_product_rating');
        });

        // Cart items table indexes
        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['member_id'], 'idx_cart_items_member');
            $table->index(['product_id'], 'idx_cart_items_product');
            $table->index(['member_id', 'product_id'], 'idx_cart_items_member_product');
            $table->index(['created_at'], 'idx_cart_items_created_at');
        });

        // Wishlist table indexes
        Schema::table('wishlists', function (Blueprint $table) {
            $table->index(['member_id'], 'idx_wishlists_member');
            $table->index(['product_id'], 'idx_wishlists_product');
            $table->index(['member_id', 'product_id'], 'idx_wishlists_member_product');
        });

        // Product images table indexes
        Schema::table('product_images', function (Blueprint $table) {
            $table->index(['product_id'], 'idx_product_images_product');
            $table->index(['is_primary'], 'idx_product_images_primary');
            $table->index(['product_id', 'is_primary'], 'idx_product_images_product_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all indexes in reverse order
        $tables = [
            'products', 'categories', 'brands', 'users',
            'orders', 'order_items', 'reviews', 'cart_items',
            'wishlists', 'product_images'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Drop all indexes for this table
                $indexes = [
                    'products' => [
                        'idx_products_name', 'idx_products_status', 'idx_products_category',
                        'idx_products_brand', 'idx_products_views', 'idx_products_status_category',
                        'idx_products_status_brand', 'idx_products_category_views', 'idx_products_price',
                        'idx_products_created_at', 'idx_products_updated_at', 'idx_products_fts',
                        'idx_products_active_only', 'idx_products_active_in_stock'
                    ],
                    'categories' => ['idx_categories_name', 'idx_categories_status', 'idx_categories_parent', 'idx_categories_created_at'],
                    'brands' => ['idx_brands_name', 'idx_brands_status', 'idx_brands_created_at'],
                    'users' => ['idx_users_email', 'idx_users_username', 'idx_users_role', 'idx_users_created_at'],
                    'orders' => ['idx_orders_user', 'idx_orders_status', 'idx_orders_created_at', 'idx_orders_updated_at', 'idx_orders_user_status', 'idx_orders_status_date'],
                    'order_items' => ['idx_order_items_order', 'idx_order_items_product', 'idx_order_items_order_product'],
                    'reviews' => ['idx_reviews_product', 'idx_reviews_member', 'idx_reviews_rating', 'idx_reviews_created_at', 'idx_reviews_product_rating'],
                    'cart_items' => ['idx_cart_items_member', 'idx_cart_items_product', 'idx_cart_items_member_product', 'idx_cart_items_created_at'],
                    'wishlists' => ['idx_wishlists_member', 'idx_wishlists_product', 'idx_wishlists_member_product'],
                    'product_images' => ['idx_product_images_product', 'idx_product_images_primary', 'idx_product_images_product_primary']
                ];

                if (isset($indexes[$tableName])) {
                    foreach ($indexes[$tableName] as $index) {
                        try {
                            $table->dropIndex($index);
                        } catch (\Exception $e) {
                            // Index might not exist, continue
                        }
                    }
                }
            });
        }
    }
};