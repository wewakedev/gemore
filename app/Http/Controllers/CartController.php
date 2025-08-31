<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Display the cart contents.
     */
    public function index(Request $request)
    {
        $cartToken = $request->cookie('cart_token');
        
        if (!$cartToken) {
            $cartItems = collect();
            $total = 0;
        } else {
            $cartItems = Cart::getCartWithProducts($cartToken);
            $total = Cart::getCartTotal($cartToken);
        }

        return view('cart', compact('cartItems', 'total'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, $productId): JsonResponse
    {
        $request->validate([
            'quantity' => 'sometimes|integer|min:1'
        ]);

        $cartToken = $request->cookie('cart_token');
        $quantity = $request->input('quantity', 1);

        // Check if product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Check if product is already in cart
        $existingCartItem = Cart::where('cart_token', $cartToken)
            ->where('product_id', $productId)
            ->first();

        if ($existingCartItem) {
            // Increment quantity
            $existingCartItem->increment('quantity', $quantity);
            $message = 'Product quantity updated in cart';
        } else {
            // Create new cart item
            Cart::create([
                'cart_token' => $cartToken,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
            $message = 'Product added to cart successfully';
        }

        // Get updated cart data
        $cartItems = Cart::getCartWithProducts($cartToken);
        $subtotal = Cart::getCartTotal($cartToken);
        $totals = $this->calculateCartTotals($subtotal, 0);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'items' => $cartItems,
                'totals' => $totals,
                'item_count' => $cartItems->count()
            ]
        ]);
    }

    /**
     * Update quantity of a product in the cart.
     */
    public function update(Request $request, $productId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartToken = $request->cookie('cart_token');
        $quantity = $request->input('quantity');

        $cartItem = Cart::where('cart_token', $cartToken)
            ->where('product_id', $productId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }

        $cartItem->update(['quantity' => $quantity]);

        // Get updated cart data
        $cartItems = Cart::getCartWithProducts($cartToken);
        $subtotal = Cart::getCartTotal($cartToken);
        $totals = $this->calculateCartTotals($subtotal, 0);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'data' => [
                'items' => $cartItems,
                'totals' => $totals,
                'item_count' => $cartItems->count()
            ]
        ]);
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(Request $request, $productId): JsonResponse
    {
        $cartToken = $request->cookie('cart_token');

        $deleted = Cart::where('cart_token', $cartToken)
            ->where('product_id', $productId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }

        // Get updated cart data
        $cartItems = Cart::getCartWithProducts($cartToken);
        $subtotal = Cart::getCartTotal($cartToken);
        $totals = $this->calculateCartTotals($subtotal, 0);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully',
            'data' => [
                'items' => $cartItems,
                'totals' => $totals,
                'item_count' => $cartItems->count()
            ]
        ]);
    }

    /**
     * Get cart data as JSON for AJAX requests.
     */
    public function getCartData(Request $request): JsonResponse
    {
        $cartToken = $request->cookie('cart_token');
        
        if (!$cartToken) {
            return response()->json([
                'success' => false,
                'message' => 'Cart token not found',
                'data' => []
            ]);
        }

        $cartItems = Cart::getCartWithProducts($cartToken);
        $subtotal = Cart::getCartTotal($cartToken);
        
        // Check for applied coupon in session
        $appliedCoupon = session('applied_coupon');
        $discountAmount = 0;
        
        if ($appliedCoupon) {
            // Validate that the coupon is still valid
            $coupon = \App\Models\Coupon::find($appliedCoupon['id']);
            if ($coupon && $coupon->isValid(null, $subtotal)['valid']) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
                // Update discount amount in session if it changed
                $appliedCoupon['discount_amount'] = $discountAmount;
                session(['applied_coupon' => $appliedCoupon]);
            } else {
                // Coupon is no longer valid, remove from session
                session()->forget('applied_coupon');
                $appliedCoupon = null;
            }
        }
        
        // Calculate totals with or without coupon
        $totals = $this->calculateCartTotals($subtotal, $discountAmount);

        return response()->json([
            'success' => true,
            'message' => 'Cart retrieved successfully',
            'data' => [
                'items' => $cartItems,
                'totals' => $totals,
                'applied_coupon' => $appliedCoupon,
                'item_count' => $cartItems->count()
            ]
        ]);
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(Request $request): JsonResponse
    {
        $cartToken = $request->cookie('cart_token');
        
        if (!$cartToken) {
            return response()->json([
                'success' => false,
                'message' => 'Cart token not found'
            ], 400);
        }

        // Delete all cart items for this cart token
        $deleted = Cart::where('cart_token', $cartToken)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'data' => [
                'items' => collect(),
                'totals' => $this->calculateCartTotals(0, 0),
                'item_count' => 0
            ]
        ]);
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