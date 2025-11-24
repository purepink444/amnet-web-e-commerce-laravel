<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // ถ้า primary key เป็น product_id แทน id
    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'product_name',
        'description',
        'price',
        'stock_quantity',
        'category_id',
        'brand_id',
        'status',
        'image_url',
        'view_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    /**
     * Get the wishlist items for the product.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id');
    }

    /**
     * Check if product is in user's wishlist.
     */
    public function isInWishlist(?int $userId = null): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->wishlists()->where('user_id', $userId)->exists();
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Get average rating for the product.
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total number of reviews for the product.
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get rating distribution for the product.
     */
    public function getRatingDistributionAttribute(): array
    {
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        $this->reviews()->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->get()
            ->each(function ($item) use (&$distribution) {
                $distribution[$item->rating] = $item->count;
            });

        return $distribution;
    }

    /**
     * Get rating stars as string.
     */
    public function getRatingStarsAttribute(): string
    {
        $rating = round($this->average_rating);
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }

    /**
     * Get rating percentage for progress bars.
     */
    public function getRatingPercentageAttribute(): float
    {
        return ($this->average_rating / 5) * 100;
    }

    /**
     * Check if product has reviews.
     */
    public function hasReviews(): bool
    {
        return $this->total_reviews > 0;
    }

    /**
     * Get latest reviews for the product.
     */
    public function getLatestReviews(int $limit = 5)
    {
        return $this->reviews()
            ->with(['member.user'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}