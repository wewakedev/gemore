<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'variant_size_id',
        'product_name',
        'variant_name',
        'variant_size',
        'quantity',
        'price',
        'original_price',
        'image',
        'sku',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that owns the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant that owns the item.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the variant size that owns the item.
     */
    public function variantSize()
    {
        return $this->belongsTo(VariantSize::class);
    }

    /**
     * Get the total price for this item.
     */
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get the discount amount for this item.
     */
    public function getDiscountAmountAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return ($this->original_price - $this->price) * $this->quantity;
        }
        return 0;
    }
}
