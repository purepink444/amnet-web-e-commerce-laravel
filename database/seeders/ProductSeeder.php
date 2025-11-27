<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unv = Brand::where('brand_name', 'UNV')->first();
        $ruijie = Brand::where('brand_name', 'Ruijie')->first();

        $cameras = Category::where('category_name', 'กล้องวงจรปิด')->first();
        $network = Category::where('category_name', 'อุปกรณ์เครือข่าย')->first();

        if ($ruijie && $network) {
            Product::firstOrCreate(['sku' => 'RG-S808C'], [
                'category_id' => $network->category_id,
                'brand_id' => $ruijie->brand_id,
                'sku' => 'RG-S808C',
                'product_name' => 'RG-S808C',
                'description' => 'Ruijie RG-S7800C Core Switch Series is specially designed for next-gen integrated network.',
                'price' => 4000.00,
                'stock_quantity' => 200,
                'specifications' => '{"ports": "48", "speed": "10G"}',
                'status' => 'active',
                'views' => 0,
            ]);
        }
    }
}