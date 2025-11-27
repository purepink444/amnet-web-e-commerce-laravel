<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // ถ้า primary key เป็น order_id แทน id (ตามที่ error hint บอก)
    protected $primaryKey = 'order_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'member_id',
        'order_date',
        'total_amount',
        'discount_amount',
        'order_status',
        'payment_status',
        'shipping_address',
        'shipping_method',
        'tracking_number',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the member that owns the order.
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    /**
     * Get the order items.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Get the payment for the order.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    /**
     * Get the shipping for the order.
     */
    public function shipping()
    {
        return $this->hasOne(Shipping::class, 'order_id', 'order_id');
    }

    /**
     * Get the status badge class for Bootstrap.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->order_status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get the status label in Thai.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->order_status) {
            'pending' => 'รอดำเนินการ',
            'confirmed' => 'ยืนยันแล้ว',
            'processing' => 'กำลังดำเนินการ',
            'shipped' => 'จัดส่งแล้ว',
            'delivered' => 'ส่งถึงแล้ว',
            'cancelled' => 'ยกเลิก',
            'refunded' => 'คืนเงิน',
            default => $this->order_status,
        };
    }
}