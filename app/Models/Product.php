<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'subcategory',
        'brand',
        'sku',
        'images',
        'tags',
        'features',
        'specifications',
        'ratings_average',
        'ratings_count',
        'is_active',
        'is_featured',
        'sort_order',
        'seo',
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'features' => 'array',
        'specifications' => 'array',
        'seo' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'ratings_average' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'GM-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the variants for the product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get active variants for the product.
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    /**
     * Get the default variant for the product.
     */
    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get approved reviews for the product.
     */
    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to search products.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    /**
     * Get the minimum price from variants.
     */
    public function getMinPriceAttribute()
    {
        return $this->variants()->min('price') ?? 0;
    }

    /**
     * Get the maximum price from variants.
     */
    public function getMaxPriceAttribute()
    {
        return $this->variants()->max('price') ?? 0;
    }

    /**
     * Get the first image.
     */
    public function getFirstImageAttribute()
    {
        $images = $this->images ?? [];
        return count($images) > 0 ? $images[0] : null;
    }

    /**
     * Get the second image.
     */
    public function getSecondImageAttribute()
    {
        $images = $this->images ?? [];
        return count($images) > 1 ? $images[1] : null;
    }

    /**
     * Get the third image.
     */
    public function getThirdImageAttribute()
    {
        $images = $this->images ?? [];
        return count($images) > 2 ? $images[2] : null;
    }

    /**
     * Get the discount price from the default variant.
     */
    public function getDiscountPriceAttribute()
    {
        $defaultVariant = $this->activeVariants()->where('is_default', true)->first();
        if ($defaultVariant && $defaultVariant->original_price && $defaultVariant->original_price > $defaultVariant->price) {
            return $defaultVariant->price;
        }
        return null;
    }

    /**
     * Get the discount percentage from the default variant.
     */
    public function getDiscountPercentageAttribute()
    {
        $defaultVariant = $this->activeVariants()->where('is_default', true)->first();
        if ($defaultVariant && $defaultVariant->original_price && $defaultVariant->original_price > $defaultVariant->price) {
            return round((($defaultVariant->original_price - $defaultVariant->price) / $defaultVariant->original_price) * 100);
        }
        return 0;
    }

    /**
     * Update product ratings.
     */
    public function updateRatings()
    {
        $reviews = $this->approvedReviews();
        $this->ratings_count = $reviews->count();
        $this->ratings_average = $reviews->avg('rating') ?? 0;
        $this->save();
    }
} 