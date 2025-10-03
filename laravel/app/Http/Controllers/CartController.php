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
            'quantity' => 'sometimes|integer|min:1',
            'variant_id' => 'sometimes|integer|exists:product_variants,id',
            'size_id' => 'sometimes|integer|exists:variant_sizes,id'
        ]);

        $cartToken = $request->cookie('cart_token');
        $quantity = $request->input('quantity', 1);
        $variantId = $request->input('variant_id');
        $sizeId = $request->input('size_id');

        // Check if product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // If variant_id is provided, validate it belongs to the product
        if ($variantId) {
            $variant = $product->variants()->find($variantId);
            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid variant for this product'
                ], 400);
            }

            // Check if variant has sizes
            $variantHasSizes = $variant->activeSizes()->count() > 0;
            
            // If variant has sizes, size_id is required
            if ($variantHasSizes && !$sizeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a size for this variant'
                ], 400);
            }

            // If size_id is provided, validate it belongs to the variant
            if ($sizeId) {
                $size = $variant->sizes()->find($sizeId);
                if (!$size) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid size for this variant'
                    ], 400);
                }

                // Check stock
                if ($size->stock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock available'
                    ], 400);
                }
            }
        }

        // Check if product with same variant and size is already in cart
        $existingCartItem = Cart::where('cart_token', $cartToken)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->where('variant_size_id', $sizeId)
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
                'product_variant_id' => $variantId,
                'variant_size_id' => $sizeId,
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
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'sometimes|integer|exists:product_variants,id',
            'size_id' => 'sometimes|integer|exists:variant_sizes,id'
        ]);

        $cartToken = $request->cookie('cart_token');
        $quantity = $request->input('quantity');
        $variantId = $request->input('variant_id');
        $sizeId = $request->input('size_id');

        // Debug logging
        \Log::info('Cart Update Request', [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'size_id' => $sizeId,
            'quantity' => $quantity,
            'cart_token' => $cartToken
        ]);

        $cartItem = Cart::where('cart_token', $cartToken)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->where('variant_size_id', $sizeId)
            ->first();

        // Debug logging
        \Log::info('Cart Item Found', [
            'found' => $cartItem ? true : false,
            'current_quantity' => $cartItem ? $cartItem->quantity : null,
            'cart_item_id' => $cartItem ? $cartItem->id : null
        ]);

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
        $request->validate([
            'variant_id' => 'sometimes|integer|exists:product_variants,id',
            'size_id' => 'sometimes|integer|exists:variant_sizes,id'
        ]);

        $cartToken = $request->cookie('cart_token');
        $variantId = $request->input('variant_id');
        $sizeId = $request->input('size_id');

        $deleted = Cart::where('cart_token', $cartToken)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->where('variant_size_id', $sizeId)
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