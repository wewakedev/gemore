<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_token',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    /**
     * Get the product that belongs to this cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant that belongs to this cart item.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Scope to get cart items by token.
     */
    public function scopeByToken($query, $token)
    {
        return $query->where('cart_token', $token);
    }

    /**
     * Get cart items with product details.
     */
    public static function getCartWithProducts($token)
    {
        return static::byToken($token)
            ->with(['product', 'product.defaultVariant', 'productVariant'])
            ->get();
    }

    /**
     * Get cart total for a specific token.
     */
    public static function getCartTotal($token)
    {
        return static::byToken($token)
            ->with(['product', 'product.defaultVariant', 'productVariant'])
            ->get()
            ->sum(function ($item) {
                // Use the selected variant price if available, otherwise use default variant
                $price = $item->productVariant?->price ?? $item->product->defaultVariant?->price ?? 0;
                return $item->quantity * $price;
            });
    }
}
