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
        'order_number',
        'total_amount',
        'status',
        'notes',
        'shipping_address',
        'payment_method',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     * ระบุ foreign key และ owner key ให้ชัดเจน
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the order items.
     * ระบุ foreign key ให้ตรงกับโครงสร้างของคุณ
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Get the status badge class for Bootstrap.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get the status label in Thai.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'รอดำเนินการ',
            'processing' => 'กำลังดำเนินการ',
            'completed' => 'สำเร็จ',
            'cancelled' => 'ยกเลิก',
            default => $this->status,
        };
    }
}