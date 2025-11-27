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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('restrict');
            $table->foreignId('brand_id')->nullable()->constrained('brands', 'brand_id')->onDelete('set null');
            $table->string('sku', 100)->unique();
            $table->string('product_name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->check('price >= 0');
            $table->integer('stock_quantity')->default(0)->check('stock_quantity >= 0');
            $table->jsonb('specifications')->nullable();
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};