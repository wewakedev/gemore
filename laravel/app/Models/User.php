<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_verified',
        'addresses',
        'wishlist',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'addresses' => 'array',
            'wishlist' => 'array',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!empty($user->password) && !Hash::needsRehash($user->password)) {
                // Password is already hashed
            } else {
                $user->password = Hash::make($user->password);
            }
        });
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the reviews for the user.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the coupon usage for the user.
     */
    public function couponUsage()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get the coupons created by the user.
     */
    public function createdCoupons()
    {
        return $this->hasMany(Coupon::class, 'created_by');
    }

    /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include customer users.
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Add product to wishlist.
     */
    public function addToWishlist($productId)
    {
        $wishlist = $this->wishlist ?? [];
        
        if (!in_array($productId, array_column($wishlist, 'product_id'))) {
            $wishlist[] = [
                'product_id' => $productId,
                'added_at' => now()->toISOString(),
            ];
            $this->wishlist = $wishlist;
            $this->save();
        }
    }

    /**
     * Remove product from wishlist.
     */
    public function removeFromWishlist($productId)
    {
        $wishlist = $this->wishlist ?? [];
        $wishlist = array_filter($wishlist, function ($item) use ($productId) {
            return $item['product_id'] != $productId;
        });
        $this->wishlist = array_values($wishlist);
        $this->save();
    }

    /**
     * Check if product is in wishlist.
     */
    public function hasInWishlist($productId)
    {
        $wishlist = $this->wishlist ?? [];
        return in_array($productId, array_column($wishlist, 'product_id'));
    }

    /**
     * Add address.
     */
    public function addAddress($address)
    {
        $addresses = $this->addresses ?? [];
        
        // If this is the first address or marked as default, make it default
        if (empty($addresses) || ($address['is_default'] ?? false)) {
            // Remove default from other addresses
            foreach ($addresses as &$addr) {
                $addr['is_default'] = false;
            }
            $address['is_default'] = true;
        }
        
        $addresses[] = $address;
        $this->addresses = $addresses;
        $this->save();
    }

    /**
     * Get default address.
     */
    public function getDefaultAddress()
    {
        $addresses = $this->addresses ?? [];
        foreach ($addresses as $address) {
            if ($address['is_default'] ?? false) {
                return $address;
            }
        }
        return $addresses[0] ?? null;
    }
}
