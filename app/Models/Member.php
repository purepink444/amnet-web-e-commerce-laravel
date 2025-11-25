<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    use HasFactory;

    protected $primaryKey = 'member_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // ตาราง members ไม่มี timestamps

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'address',
        'district',
        'province',
        'postal_code',
        'profile_image',
        'membership_level',
        'points',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the reviews for the member.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'member_id', 'member_id');
    }

    /**
     * Get the orders for the member.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'member_id', 'member_id');
    }


    /**
     * Get the wishlists for the member.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'member_id', 'member_id');
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get membership level label in Thai.
     */
    public function getMembershipLevelLabelAttribute(): string
    {
        return match($this->membership_level) {
            'bronze' => 'บรอนซ์',
            'silver' => 'ซิลเวอร์',
            'gold' => 'โกลด์',
            'platinum' => 'แพลทินัม',
            default => $this->membership_level,
        };
    }

    /**
     * Get membership level color for Bootstrap.
     */
    public function getMembershipLevelColorAttribute(): string
    {
        return match($this->membership_level) {
            'bronze' => 'secondary',
            'silver' => 'info',
            'gold' => 'warning',
            'platinum' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Check if member can review a product.
     */
    public function canReviewProduct(int $productId): bool
    {
        // Check if member has purchased this product
        return $this->orders()
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->where('order_status', 'delivered')
            ->exists();
    }

    /**
     * Check if member has already reviewed a product.
     */
    public function hasReviewedProduct(int $productId): bool
    {
        return $this->reviews()->where('product_id', $productId)->exists();
    }
}