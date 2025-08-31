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
        $total = Cart::getCartTotal($cartToken);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'items' => $cartItems,
                'total' => $total,
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
        $total = Cart::getCartTotal($cartToken);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'data' => [
                'items' => $cartItems,
                'total' => $total,
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
        $total = Cart::getCartTotal($cartToken);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully',
            'data' => [
                'items' => $cartItems,
                'total' => $total,
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
        $total = Cart::getCartTotal($cartToken);

        return response()->json([
            'success' => true,
            'message' => 'Cart retrieved successfully',
            'data' => [
                'items' => $cartItems,
                'total' => $total,
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
                'total' => 0,
                'item_count' => 0
            ]
        ]);
    }


} 