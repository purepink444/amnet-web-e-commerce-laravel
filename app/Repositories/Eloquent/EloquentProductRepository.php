<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findByIdWithRelations(int $id): ?Product
    {
        return Product::with([
            'category:id,category_name',
            'brand:id,brand_name',
            'images' => function ($query) {
                $query->select('product_id', 'image_path', 'is_primary')
                      ->orderBy('is_primary', 'desc')
                      ->orderBy('sort_order');
            },
            'reviews' => function ($query) {
                $query->select('product_id', 'rating', 'review_text', 'created_at', 'member_id')
                      ->with('member:id,first_name,last_name')
                      ->latest()
                      ->limit(5);
            }
        ])->find($id);
    }

    public function getActiveWithRelations(): Collection
    {
        return Product::with(['category', 'brand', 'primaryImage'])
                     ->active()
                     ->inStock()
                     ->get();
    }

    public function search(array $filters = []): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['category:id,category_name', 'brand:id,brand_name', 'primaryImage']);

        // Apply filters
        $query->when($filters['category_id'] ?? null, function ($q) use ($filters) {
            return $q->where('category_id', $filters['category_id']);
        });

        $query->when($filters['brand_id'] ?? null, function ($q) use ($filters) {
            return $q->where('brand_id', $filters['brand_id']);
        });

        $query->when($filters['status'] ?? null, function ($q) use ($filters) {
            return $q->where('status', $filters['status']);
        });

        $query->when($filters['search'] ?? null, function ($q) use ($filters) {
            $searchTerm = $filters['search'];
            return $q->where(function ($query) use ($searchTerm) {
                $query->where('product_name', 'ILIKE', '%' . $searchTerm . '%')
                      ->orWhere('description', 'ILIKE', '%' . $searchTerm . '%')
                      ->orWhere('sku', 'ILIKE', '%' . $searchTerm . '%');
            });
        });

        $query->when($filters['price_min'] ?? null, function ($q) use ($filters) {
            return $q->where('price', '>=', $filters['price_min']);
        });

        $query->when($filters['price_max'] ?? null, function ($q) use ($filters) {
            return $q->where('price', '<=', $filters['price_max']);
        });

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        switch ($sortBy) {
            case 'name':
                $query->orderBy('product_name', $sortDirection);
                break;
            case 'price':
                $query->orderBy('price', $sortDirection);
                break;
            case 'views':
                $query->orderBy('views', $sortDirection);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $sortDirection);
                break;
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->findById($id);
        return $product ? $product->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $product = $this->findById($id);
        return $product ? $product->delete() : false;
    }

    public function exists(int $id): bool
    {
        return Product::where('product_id', $id)->exists();
    }

    public function countByStatus(string $status): int
    {
        return Product::where('status', $status)->count();
    }

    public function getByCategory(int $categoryId): Collection
    {
        return Product::where('category_id', $categoryId)
                     ->active()
                     ->inStock()
                     ->get();
    }

    public function getByBrand(int $brandId): Collection
    {
        return Product::where('brand_id', $brandId)
                     ->active()
                     ->inStock()
                     ->get();
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return Product::with(['category', 'brand', 'primaryImage'])
                     ->active()
                     ->inStock()
                     ->orderBy('views', 'desc')
                     ->limit($limit)
                     ->get();
    }

    public function getTrending(int $days = 7, int $limit = 10): Collection
    {
        $date = now()->subDays($days);

        return Product::with(['category', 'brand', 'primaryImage'])
                     ->active()
                     ->inStock()
                     ->where('created_at', '>=', $date)
                     ->orderBy('views', 'desc')
                     ->limit($limit)
                     ->get();
    }

    public function incrementViews(int $id): bool
    {
        return Product::where('product_id', $id)->increment('views') > 0;
    }

    public function updateStock(int $id, int $quantity): bool
    {
        return Product::where('product_id', $id)->update(['stock_quantity' => $quantity]) > 0;
    }
}