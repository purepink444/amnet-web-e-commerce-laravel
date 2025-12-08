<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $primaryKey = 'review_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'member_id',
        'product_id',
        'rating',
        'comment',
        'review_images',
        'is_verified_purchase',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'review_images' => 'json',
        'is_verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
    ];

    /**
     * Get the member that owns the review.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    /**
     * Get the product for the review.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get rating stars HTML.
     */
    public function getRatingStarsAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $this->rating
                ? '<i class="bi bi-star-fill text-warning"></i>'
                : '<i class="bi bi-star text-warning"></i>';
        }
        return $stars;
    }
}
