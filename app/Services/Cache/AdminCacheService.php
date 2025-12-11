<?php

namespace App\Services\Cache;

use App\Services\Cache\AdvancedCacheService;
use Illuminate\Support\Collection;

class AdminCacheService
{
    public function __construct(
        private AdvancedCacheService $cacheService
    ) {}

    /**
     * Get cached dropdown data for product forms
     */
    public function getProductDropdowns(): array
    {
        return [
            'categories' => $this->cacheService->getCategories(),
            'brands' => $this->cacheService->getBrands(),
        ];
    }

    /**
     * Get cached categories for admin
     */
    public function getCategories(): Collection
    {
        return $this->cacheService->rememberWithTags(
            'admin_categories_dropdown',
            ['categories'],
            3600, // 1 hour
            function () {
                return \App\Models\Category::select('category_id', 'category_name')
                                          ->orderBy('category_name')
                                          ->get();
            }
        );
    }

    /**
     * Get cached brands for admin
     */
    public function getBrands(): Collection
    {
        return $this->cacheService->rememberWithTags(
            'admin_brands_dropdown',
            ['brands'],
            3600, // 1 hour
            function () {
                return \App\Models\Brand::select('brand_id', 'brand_name')
                                       ->orderBy('brand_name')
                                       ->get();
            }
        );
    }

    /**
     * Invalidate all product-related caches
     */
    public function invalidateProductRelatedCache(): void
    {
        $this->cacheService->invalidateProductRelatedCache();
    }

    /**
     * Invalidate category-related caches
     */
    public function invalidateCategoryRelatedCache(): void
    {
        $this->cacheService->invalidateCategoryRelatedCache();
    }

    /**
     * Invalidate brand-related caches
     */
    public function invalidateBrandRelatedCache(): void
    {
        $this->cacheService->invalidateBrandRelatedCache();
    }

    /**
     * Get cache statistics for admin dashboard
     */
    public function getCacheStats(): array
    {
        return $this->cacheService->getCacheStats();
    }

    /**
     * Clear all admin-related caches
     */
    public function clearAllAdminCaches(): void
    {
        $this->invalidateProductRelatedCache();
        $this->invalidateCategoryRelatedCache();
        $this->invalidateBrandRelatedCache();
    }
}