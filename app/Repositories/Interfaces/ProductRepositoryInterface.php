<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function findByIdWithRelations(int $id): ?Product;
    public function getActiveWithRelations(): Collection;
    public function search(array $filters = []): LengthAwarePaginator;
    public function create(array $data): Product;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function exists(int $id): bool;
    public function countByStatus(string $status): int;
    public function getByCategory(int $categoryId): Collection;
    public function getByBrand(int $brandId): Collection;
    public function getFeatured(int $limit = 10): Collection;
    public function getTrending(int $days = 7, int $limit = 10): Collection;
    public function incrementViews(int $id): bool;
    public function updateStock(int $id, int $quantity): bool;
}