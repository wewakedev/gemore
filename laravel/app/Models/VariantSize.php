<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'size_name',
        'size_display_name',
        'price',
        'original_price',
        'stock',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    /**
     * Get the product variant that owns the size.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the cart items for the variant size.
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the order items for the variant size.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to only include active sizes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include in-stock sizes.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Check if size is in stock.
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return 0;
    }

    /**
     * Get the display name or fall back to size_name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->size_display_name ?: $this->size_name;
    }
}

