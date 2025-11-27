<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $primaryKey = 'member_id';
    public $incrementing = true;
    protected $keyType = 'int';

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
    ];

    /**
     * Get the user that owns the member.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the orders for the member.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'member_id', 'member_id');
    }

    /**
     * Get the cart items for the member.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'member_id', 'member_id');
    }

    /**
     * Get the reviews for the member.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'member_id', 'member_id');
    }

    /**
     * Get the wishlists for the member.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'member_id', 'member_id');
    }
}
