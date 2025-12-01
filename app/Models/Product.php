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
        'category_id',
        'brand_id',
        'sku',
        'product_name',
        'description',
        'price',
        'stock_quantity',
        'specifications',
        'status',
        'views',
        'photo_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'specifications' => 'json',
        'views' => 'integer',
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
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')->where('is_primary', true);
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    /**
     * Get the wishlists for the product.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id', 'product_id');
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
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
     * Check if product has reviews.
     */
    public function hasReviews()
    {
        return $this->reviews()->exists();
    }

    /**
     * Get average rating.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count.
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Get rating stars HTML.
     */
    public function getRatingStarsAttribute()
    {
        $rating = $this->average_rating;
        $stars = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<i class="bi bi-star-fill text-warning"></i>';
            } elseif ($i - 0.5 <= $rating) {
                $stars .= '<i class="bi bi-star-half text-warning"></i>';
            } else {
                $stars .= '<i class="bi bi-star text-warning"></i>';
            }
        }

        return $stars;
    }

    /**
     * Get rating distribution.
     */
    public function getRatingDistributionAttribute()
    {
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $this->reviews()->where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Get latest reviews.
     */
    public function getLatestReviews($limit = 3)
    {
        return $this->reviews()->with('member.user')->latest()->limit($limit)->get();
    }
}