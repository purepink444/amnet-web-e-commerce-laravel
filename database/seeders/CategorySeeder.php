<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::firstOrCreate(['category_name' => 'กล้องวงจรปิด'], [
            'description' => 'ระบบกล้องวงจรปิดทุกประเภท',
            'is_active' => true,
        ]);

        Category::firstOrCreate(['category_name' => 'อุปกรณ์เครือข่าย'], [
            'description' => 'สวิตช์, เราเตอร์, ไวไฟ',
            'is_active' => true,
        ]);

        Category::firstOrCreate(['category_name' => 'IoT & AI'], [
            'description' => 'อุปกรณ์ AI และ IoT',
            'is_active' => true,
        ]);

        Category::firstOrCreate(['category_name' => 'IP Camera'], [
            'parent_category_id' => 1,
            'description' => null,
            'is_active' => true,
        ]);

        Category::firstOrCreate(['category_name' => 'PTZ Camera'], [
            'parent_category_id' => 1,
            'description' => null,
            'is_active' => true,
        ]);

        Category::firstOrCreate(['category_name' => 'NVR/DVR'], [
            'parent_category_id' => 1,
            'description' => null,
            'is_active' => true,
        ]);

        Category::firstOrCreate(['category_name' => 'Switches'], [
            'parent_category_id' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        Category::firstOrCreate(['category_name' => 'Router'], [
            'parent_category_id' => 2,
            'description' => null,
            'is_active' => true,
        ]);

        Category::firstOrCreate(['category_name' => 'Wireless'], [
            'parent_category_id' => 2,
            'description' => null,
            'is_active' => true,
        ]);
    }
}