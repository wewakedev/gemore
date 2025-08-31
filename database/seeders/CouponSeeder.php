<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $futureDate = $now->copy()->addMonths(6); // Valid for 6 months

        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => 'Get 10% off on your first order! Valid for all customers.',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_order_amount' => 500.00,
                'maximum_discount' => 500.00,
                'usage_limit_total' => null, // Unlimited
                'usage_limit_per_user' => 3,
                'usage_count' => 0,
                'valid_from' => $now,
                'valid_until' => $futureDate,
                'applicable_products' => null,
                'applicable_categories' => null,
                'exclude_products' => null,
                'exclude_categories' => null,
                'is_active' => true,
                'is_first_time_user' => false,
                'created_by' => null,
            ],
            [
                'code' => 'SAVE100',
                'name' => 'Flat ₹100 Off',
                'description' => 'Get flat ₹100 off on orders above ₹1000. Perfect for bulk purchases!',
                'type' => 'fixed',
                'value' => 100.00,
                'minimum_order_amount' => 1000.00,
                'maximum_discount' => null,
                'usage_limit_total' => null, // Unlimited
                'usage_limit_per_user' => 5,
                'usage_count' => 0,
                'valid_from' => $now,
                'valid_until' => $futureDate,
                'applicable_products' => null,
                'applicable_categories' => null,
                'exclude_products' => null,
                'exclude_categories' => null,
                'is_active' => true,
                'is_first_time_user' => false,
                'created_by' => null,
            ],
            [
                'code' => 'NEWUSER15',
                'name' => 'New User Special',
                'description' => 'Exclusive 15% discount for first-time customers. Welcome to Ge More!',
                'type' => 'percentage',
                'value' => 15.00,
                'minimum_order_amount' => 750.00,
                'maximum_discount' => 750.00,
                'usage_limit_total' => null, // Unlimited
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
                'valid_from' => $now,
                'valid_until' => $futureDate,
                'applicable_products' => null,
                'applicable_categories' => null,
                'exclude_products' => null,
                'exclude_categories' => null,
                'is_active' => true,
                'is_first_time_user' => true, // Only for first-time users
                'created_by' => null,
            ],
            [
                'code' => 'MEGA20',
                'name' => 'Mega Sale',
                'description' => 'Huge 20% discount on orders above ₹2000. Limited time offer!',
                'type' => 'percentage',
                'value' => 20.00,
                'minimum_order_amount' => 2000.00,
                'maximum_discount' => 1000.00,
                'usage_limit_total' => 1000, // Limited to 1000 uses
                'usage_limit_per_user' => 2,
                'usage_count' => 0,
                'valid_from' => $now,
                'valid_until' => $futureDate,
                'applicable_products' => null,
                'applicable_categories' => null,
                'exclude_products' => null,
                'exclude_categories' => null,
                'is_active' => true,
                'is_first_time_user' => false,
                'created_by' => null,
            ],
            [
                'code' => 'FLAT200',
                'name' => 'Big Savings',
                'description' => 'Save ₹200 on orders above ₹3000. Great for large orders!',
                'type' => 'fixed',
                'value' => 200.00,
                'minimum_order_amount' => 3000.00,
                'maximum_discount' => null,
                'usage_limit_total' => null, // Unlimited
                'usage_limit_per_user' => 3,
                'usage_count' => 0,
                'valid_from' => $now,
                'valid_until' => $futureDate,
                'applicable_products' => null,
                'applicable_categories' => null,
                'exclude_products' => null,
                'exclude_categories' => null,
                'is_active' => true,
                'is_first_time_user' => false,
                'created_by' => null,
            ],
            [
                'code' => 'QUICK5',
                'name' => 'Quick Discount',
                'description' => '5% off on any order. No minimum purchase required!',
                'type' => 'percentage',
                'value' => 5.00,
                'minimum_order_amount' => 0.00,
                'maximum_discount' => 250.00,
                'usage_limit_total' => null, // Unlimited
                'usage_limit_per_user' => 10,
                'usage_count' => 0,
                'valid_from' => $now,
                'valid_until' => $futureDate,
                'applicable_products' => null,
                'applicable_categories' => null,
                'exclude_products' => null,
                'exclude_categories' => null,
                'is_active' => true,
                'is_first_time_user' => false,
                'created_by' => null,
            ],
            [
                'code' => 'PREMIUM25',
                'name' => 'Premium Discount',
                'description' => 'Exclusive 25% off for premium customers on orders above ₹4000.',
                'type' => 'percentage',
                'value' => 25.00,
                'minimum_order_amount' => 4000.00,
                'maximum_discount' => 1500.00,
                'usage_limit_total' => 500, // Limited to 500 uses
                'usage_limit_per_user' => 1,
                'usage_count' => 0,
                'valid_from' => $now,
                'valid_until' => $futureDate,
                'applicable_products' => null,
                'applicable_categories' => null,
                'exclude_products' => null,
                'exclude_categories' => null,
                'is_active' => true,
                'is_first_time_user' => false,
                'created_by' => null,
            ],
            [
                'code' => 'SAVE50',
                'name' => 'Starter Savings',
                'description' => 'Save ₹50 on orders above ₹500. Perfect for small orders!',
                'type' => 'fixed',
                'value' => 50.00,
                'minimum_order_amount' => 500.00,
                'maximum_discount' => null,
                'usage_limit_total' => null, // Unlimited
                'usage_limit_per_user' => 5,
                'usage_count' => 0,
                'valid_from' => $now,
                'valid_until' => $futureDate,
                'applicable_products' => null,
                'applicable_categories' => null,
                'exclude_products' => null,
                'exclude_categories' => null,
                'is_active' => true,
                'is_first_time_user' => false,
                'created_by' => null,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::updateOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }

        $this->command->info('Created ' . count($coupons) . ' dummy coupons successfully!');
    }
}
