<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order_amount',
        'maximum_discount',
        'usage_limit_total',
        'usage_limit_per_user',
        'usage_count',
        'valid_from',
        'valid_until',
        'applicable_products',
        'applicable_categories',
        'exclude_products',
        'exclude_categories',
        'is_active',
        'is_first_time_user',
        'created_by',
    ];

    protected $casts = [
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'exclude_products' => 'array',
        'exclude_categories' => 'array',
        'is_active' => 'boolean',
        'is_first_time_user' => 'boolean',
        'value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($coupon) {
            $coupon->code = strtoupper($coupon->code);
        });

        static::updating(function ($coupon) {
            $coupon->code = strtoupper($coupon->code);
        });
    }

    /**
     * Get the user who created the coupon.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the orders that used this coupon.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the coupon usage records.
     */
    public function usage()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include valid coupons.
     */
    public function scopeValid($query)
    {
        $now = now();
        return $query->where('valid_from', '<=', $now)
                    ->where('valid_until', '>=', $now);
    }

    /**
     * Check if coupon is valid.
     */
    public function isValid($userId = null, $orderAmount = 0)
    {
        // Check if active
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Coupon is not active'];
        }

        // Check date validity
        $now = now();
        if ($this->valid_from > $now || $this->valid_until < $now) {
            return ['valid' => false, 'message' => 'Coupon has expired or is not yet valid'];
        }

        // Check minimum order amount
        if ($orderAmount < $this->minimum_order_amount) {
            return ['valid' => false, 'message' => "Minimum order amount is ₹{$this->minimum_order_amount}"];
        }

        // Check total usage limit
        if ($this->usage_limit_total && $this->usage_count >= $this->usage_limit_total) {
            return ['valid' => false, 'message' => 'Coupon usage limit exceeded'];
        }

        // Check per-user usage limit
        if ($userId && $this->usage_limit_per_user) {
            $userUsageCount = $this->usage()->where('user_id', $userId)->sum('used_count');
            if ($userUsageCount >= $this->usage_limit_per_user) {
                return ['valid' => false, 'message' => 'You have already used this coupon'];
            }
        }

        // Check if for first-time users only
        if ($this->is_first_time_user && $userId) {
            $userOrderCount = Order::where('user_id', $userId)->where('status', '!=', 'cancelled')->count();
            if ($userOrderCount > 0) {
                return ['valid' => false, 'message' => 'This coupon is only for first-time users'];
            }
        }

        return ['valid' => true, 'message' => 'Coupon is valid'];
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount($orderAmount)
    {
        if ($this->type === 'percentage') {
            $discount = ($orderAmount * $this->value) / 100;
            if ($this->maximum_discount) {
                $discount = min($discount, $this->maximum_discount);
            }
            return $discount;
        } else {
            return min($this->value, $orderAmount);
        }
    }

    /**
     * Apply coupon usage.
     */
    public function applyUsage($userId, $orderId, $discountAmount)
    {
        // Create usage record
        CouponUsage::create([
            'coupon_id' => $this->id,
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
        ]);

        // Increment usage count
        $this->increment('usage_count');
    }
}
