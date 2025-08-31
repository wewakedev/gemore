<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    /**
     * Apply a coupon to the cart
     */
    public function apply(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'coupon_code' => 'required|string|max:50'
            ]);

            $cartToken = $request->cookie('cart_token');
            $couponCode = strtoupper(trim($request->input('coupon_code')));

            if (!$cartToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 400);
            }

            // Get cart items and calculate subtotal
            $cartItems = Cart::getCartWithProducts($cartToken);
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            $subtotal = Cart::getCartTotal($cartToken);

            // Find the coupon
            $coupon = Coupon::where('code', $couponCode)
                ->active()
                ->valid()
                ->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired coupon code'
                ], 400);
            }

            // Validate coupon
            $validation = $coupon->isValid(null, $subtotal);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ], 400);
            }

            // Calculate discount
            $discountAmount = $coupon->calculateDiscount($subtotal);

            // Store applied coupon in session
            $appliedCouponData = [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount_amount' => $discountAmount,
                'applied_at' => now()->toISOString()
            ];
            
            session(['applied_coupon' => $appliedCouponData]);

            // Calculate totals with coupon
            $totals = $this->calculateCartTotals($subtotal, $discountAmount);

            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully',
                'data' => [
                    'coupon' => $appliedCouponData,
                    'totals' => $totals
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code format',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Coupon apply error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while applying the coupon'
            ], 500);
        }
    }

    /**
     * Remove coupon from cart
     */
    public function remove(Request $request): JsonResponse
    {
        try {
            $cartToken = $request->cookie('cart_token');

            if (!$cartToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 400);
            }

            // Remove coupon from session
            session()->forget('applied_coupon');

            // Get cart total without coupon
            $subtotal = Cart::getCartTotal($cartToken);
            $totals = $this->calculateCartTotals($subtotal, 0);

            return response()->json([
                'success' => true,
                'message' => 'Coupon removed successfully',
                'data' => [
                    'totals' => $totals
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Coupon remove error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the coupon'
            ], 500);
        }
    }

    /**
     * Validate a coupon without applying it
     */
    public function validate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'coupon_code' => 'required|string|max:50'
            ]);

            $cartToken = $request->cookie('cart_token');
            $couponCode = strtoupper(trim($request->input('coupon_code')));

            if (!$cartToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 400);
            }

            $subtotal = Cart::getCartTotal($cartToken);

            // Find the coupon
            $coupon = Coupon::where('code', $couponCode)
                ->active()
                ->valid()
                ->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired coupon code'
                ], 400);
            }

            // Validate coupon
            $validation = $coupon->isValid(null, $subtotal);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ], 400);
            }

            // Calculate potential discount
            $discountAmount = $coupon->calculateDiscount($subtotal);

            return response()->json([
                'success' => true,
                'message' => 'Coupon is valid',
                'data' => [
                    'coupon' => [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'name' => $coupon->name,
                        'type' => $coupon->type,
                        'value' => $coupon->value,
                        'discount_amount' => $discountAmount
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code format',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Coupon validate error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while validating the coupon'
            ], 500);
        }
    }

    /**
     * Calculate cart totals with shipping, tax, and discount
     */
    private function calculateCartTotals(float $subtotal, float $discountAmount = 0): array
    {
        $freeDeliveryAmount = config('app.free_delivery_order_amount', 5000);
        
        // Apply discount to subtotal
        $discountedSubtotal = max(0, $subtotal - $discountAmount);
        
        // Calculate shipping based on discounted subtotal
        $shipping = $discountedSubtotal >= $freeDeliveryAmount ? 0 : 100;
        
        // Calculate tax on discounted subtotal (18% GST)
        $tax = $discountedSubtotal * 0.18;
        
        // Calculate final total
        $total = $discountedSubtotal + $shipping + $tax;

        return [
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'discounted_subtotal' => $discountedSubtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'free_delivery_amount' => $freeDeliveryAmount
        ];
    }
}
