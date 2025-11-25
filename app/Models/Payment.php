<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; // ตาราง payments ไม่มี updated_at

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_date',
        'transaction_id',
        'payment_proof_url',
        'payment_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    public function markAsCompleted()
    {
        $this->update([
            'payment_status' => 'completed',
            'payment_date' => now(),
        ]);
    }

    public function canRetry()
    {
        return $this->payment_status === 'failed' &&
               (!isset($this->payment_data['retry_count']) || $this->payment_data['retry_count'] < 3);
    }

    public function incrementRetryCount()
    {
        $retryCount = ($this->payment_data['retry_count'] ?? 0) + 1;
        $this->update([
            'payment_data' => array_merge($this->payment_data ?? [], [
                'retry_count' => $retryCount,
                'last_retry_at' => now(),
            ])
        ]);
    }
}
