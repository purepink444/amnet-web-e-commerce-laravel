<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $primaryKey = 'cart_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'member_id',
        'product_id',
        'quantity',
        'price_at_add',
        'added_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_add' => 'decimal:2',
        'added_at' => 'datetime',
    ];

    /**
     * Get the member that owns the cart item.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    /**
     * Get the product for the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
