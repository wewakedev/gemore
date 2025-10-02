<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'shipping_address',
        'billing_address',
        'payment',
        'subtotal',
        'discount',
        'shipping',
        'tax',
        'total',
        'coupon_id',
        'coupon_code',
        'coupon_discount',
        'status',
        'tracking',
        'status_history',
        'customer_notes',
        'admin_notes',
        'cancellation',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'payment' => 'array',
        'tracking' => 'array',
        'status_history' => 'array',
        'cancellation' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'GM' . substr(time(), -8);
            }
        });

        static::updating(function ($order) {
            if ($order->isDirty('status')) {
                $statusHistory = $order->status_history ?? [];
                $statusHistory[] = [
                    'status' => $order->status,
                    'timestamp' => now()->toISOString(),
                    'note' => null,
                ];
                $order->status_history = $statusHistory;
            }
        });
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the coupon used in the order.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the coupon usage for the order.
     */
    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if order is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'delivered';
    }

    /**
     * Get the total items count.
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
} 