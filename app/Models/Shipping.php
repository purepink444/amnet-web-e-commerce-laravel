<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $primaryKey = 'shipping_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'order_id',
        'shipping_company',
        'tracking_number',
        'shipping_status',
        'shipped_date',
        'delivered_date',
    ];

    protected $casts = [
        'shipped_date' => 'datetime',
        'delivered_date' => 'datetime',
    ];

    /**
     * Get the order that owns the shipping.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
