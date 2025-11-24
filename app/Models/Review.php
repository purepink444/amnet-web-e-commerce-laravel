<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'review_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'product_id',
        'member_id',
        'rating',
        'comment',
        'review_images',
    ];

    protected $casts = [
        'review_images' => 'array',
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the review.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the member that owns the review.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    /**
     * Get the user that owns the review through member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id', 'member_id');
    }

    /**
     * Get rating stars as string.
     */
    public function getRatingStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get rating percentage for progress bars.
     */
    public function getRatingPercentageAttribute(): float
    {
        return ($this->rating / 5) * 100;
    }

    /**
     * Get rating label in Thai.
     */
    public function getRatingLabelAttribute(): string
    {
        return match($this->rating) {
            1 => 'แย่มาก',
            2 => 'แย่',
            3 => 'ปานกลาง',
            4 => 'ดี',
            5 => 'ดีมาก',
            default => 'ไม่ระบุ',
        };
    }

    /**
     * Scope for filtering by rating.
     */
    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope for filtering by product.
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope for filtering by member.
     */
    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    /**
     * Scope for ordering by latest reviews.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for ordering by highest rating.
     */
    public function scopeHighestRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    /**
     * Check if review has images.
     */
    public function hasImages(): bool
    {
        return !empty($this->review_images) && is_array($this->review_images);
    }

    /**
     * Get review images as array.
     */
    public function getImages(): array
    {
        return $this->hasImages() ? $this->review_images : [];
    }
}