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
        Brand::updateOrCreate(['brand_name' => 'Dahua'], [
            'logo_url' => 'images/brands/dahua.png',
            'description' => 'Dahua - กล้องวงจรปิดและระบบรักษาความปลอดภัย',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'Hikvision'], [
            'logo_url' => 'images/brands/hikvision.png',
            'description' => 'Hikvision - ผู้นำด้านกล้องวงจรปิด',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'Ruijie'], [
            'logo_url' => 'images/brands/ruijie.png',
            'description' => 'Ruijie Networks - อุปกรณ์เครือข่าย',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'Mikrotik'], [
            'logo_url' => 'images/brands/mikrotik.png',
            'description' => 'Mikrotik - Router และอุปกรณ์เครือข่าย',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'Reyee'], [
            'logo_url' => 'images/brands/reyee.png',
            'description' => 'Reyee - ระบบเครือข่ายไร้สาย',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'H3C'], [
            'logo_url' => 'images/brands/h3c.png',
            'description' => 'H3C - Enterprise Network Solutions',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'MEGVII'], [
            'logo_url' => 'images/brands/megvii.png',
            'description' => 'MEGVII - AI และ Computer Vision',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'BDCOM'], [
            'logo_url' => 'images/brands/bdcom.png',
            'description' => 'BDCOM - Optical Communication',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'Uniview'], [
            'logo_url' => 'images/brands/uniview.png',
            'description' => 'Uniview - กล้องวงจรปิด HD',
            'is_active' => true,
        ]);

        Brand::updateOrCreate(['brand_name' => 'Samcom'], [
            'logo_url' => 'images/brands/samcom.png',
            'description' => 'Samcom - AI Camera Solutions',
            'is_active' => true,
        ]);
    }
}