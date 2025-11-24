<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $primaryKey = 'wishlist_id';
    public $timestamps = false; // ใช้ added_at แทน

    protected $fillable = [
        'member_id',
        'product_id',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    /**
     * Get the member that owns the wishlist item.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    /**
     * Get the product that is in the wishlist.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
