<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::firstOrCreate(['brand_name' => 'UNV'], [
            'logo_url' => null,
            'description' => 'UNV - กล้องวงจรปิดคุณภาพสูง',
            'is_active' => true,
        ]);

        Brand::firstOrCreate(['brand_name' => 'Ruijie'], [
            'logo_url' => null,
            'description' => 'Ruijie Networks - อุปกรณ์เครือข่าย',
            'is_active' => true,
        ]);

        Brand::firstOrCreate(['brand_name' => 'H3C'], [
            'logo_url' => null,
            'description' => 'H3C - Enterprise Network Solutions',
            'is_active' => true,
        ]);

        Brand::firstOrCreate(['brand_name' => 'Tiandy'], [
            'logo_url' => null,
            'description' => 'Tiandy - AI Security Camera',
            'is_active' => true,
        ]);

        Brand::firstOrCreate(['brand_name' => 'SAMCOM'], [
            'logo_url' => null,
            'description' => 'SAMCOM - AI Camera Solutions',
            'is_active' => true,
        ]);
    }
}