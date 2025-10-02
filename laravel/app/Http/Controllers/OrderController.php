<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\PhonePeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Process checkout form submission
     */
    public function processCheckout(Request $request): JsonResponse
    {
        try {
            // Validate the checkout form data
            $validated = $request->validate([
                'billing-name' => 'required|string|max:255',
                'billing-email' => 'required|email|max:255',
                'billing-phone' => 'required|string|max:20',
                'billing-address' => 'required|string',
                'billing-city' => 'nullable|string|max:100',
                'billing-state' => 'required|string|max:100',
                'billing-pincode' => 'nullable|string|max:10',
                'shipping-name' => 'nullable|string|max:255',
                'shipping-address' => 'nullable|string',
                'shipping-city' => 'nullable|string|max:100',
                'shipping-state' => 'nullable|string|max:100',
                'shipping-pincode' => 'nullable|string|max:10',
                'shipping-phone' => 'nullable|string|max:20',
                'payment-method' => 'required|in:cod,online',
                'order-notes' => 'nullable|string|max:1000',
                'same-as-billing' => 'nullable|boolean'
            ]);

            $cartToken = $request->cookie('cart_token');
            
            if (!$cartToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 400);
            }

            // Get cart items
            $cartItems = Cart::getCartWithProducts($cartToken);
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Get applied coupon from session
            $appliedCoupon = session('applied_coupon');
            $couponDiscount = 0;
            $couponCode = null;
            $couponId = null;

            if ($appliedCoupon) {
                $couponDiscount = $appliedCoupon['discount_amount'] ?? 0;
                $couponCode = $appliedCoupon['code'] ?? null;
                $couponId = $appliedCoupon['id'] ?? null;
            }

            // Calculate totals
            $subtotal = Cart::getCartTotal($cartToken);
            $discountedSubtotal = $subtotal - $couponDiscount;
            $freeDeliveryAmount = config('app.free_delivery_order_amount', 5000);
            $shipping = $discountedSubtotal >= $freeDeliveryAmount ? 0 : 100;
            $tax = $subtotal * 0.18; // 18% GST
            $total = $subtotal + $shipping + $tax - $couponDiscount;

            // Prepare addresses
            $billingAddress = [
                'name' => $validated['billing-name'],
                'address' => $validated['billing-address'],
                'city' => $validated['billing-city'] ?? '',
                'state' => $validated['billing-state'] ?? '',
                'pincode' => $validated['billing-pincode'] ?? '',
                'phone' => $validated['billing-phone']
            ];

            $shippingAddress = $validated['same-as-billing'] ?? true ? $billingAddress : [
                'name' => $validated['shipping-name'] ?? $validated['billing-name'],
                'address' => $validated['shipping-address'] ?? $validated['billing-address'],
                'city' => $validated['shipping-city'] ?? $validated['billing-city'] ?? '',
                'state' => $validated['shipping-state'] ?? $validated['billing-state'] ?? '',
                'pincode' => $validated['shipping-pincode'] ?? $validated['billing-pincode'] ?? '',
                'phone' => $validated['shipping-phone'] ?? $validated['billing-phone']
            ];

            $paymentMethod = $validated['payment-method'];

            if ($paymentMethod === 'cod') {
                // Process COD order
                return $this->processCODOrder(
                    $cartToken,
                    $cartItems,
                    $billingAddress,
                    $shippingAddress,
                    $subtotal,
                    $shipping,
                    $tax,
                    $total,
                    $validated['billing-email'],
                    $validated['order-notes'] ?? null,
                    $couponId,
                    $couponCode,
                    $couponDiscount
                );
            } else {
                // Process online payment
                return $this->processOnlinePayment(
                    $cartToken,
                    $cartItems,
                    $billingAddress,
                    $shippingAddress,
                    $subtotal,
                    $shipping,
                    $tax,
                    $total,
                    $validated['billing-email'],
                    $validated['order-notes'] ?? null,
                    $couponId,
                    $couponCode,
                    $couponDiscount
                );
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your order. Please try again.'
            ], 500);
        }
    }

    /**
     * Process COD order
     */
    private function processCODOrder(
        string $cartToken,
        $cartItems,
        array $billingAddress,
        array $shippingAddress,
        float $subtotal,
        float $shipping,
        float $tax,
        float $total,
        string $email,
        ?string $notes,
        ?int $couponId = null,
        ?string $couponCode = null,
        float $couponDiscount = 0
    ): JsonResponse {
        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => null, // Guest checkout
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'payment' => [
                    'method' => 'cod',
                    'status' => 'pending'
                ],
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'total' => $total,
                'coupon_id' => $couponId,
                'coupon_code' => $couponCode,
                'coupon_discount' => $couponDiscount,
                'status' => 'confirmed',
                'customer_notes' => $notes
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->defaultVariant?->price ?? 0,
                    'product_name' => $cartItem->product->name,
                    'variant_name' => $cartItem->product->defaultVariant?->name ?? 'Default'
                ]);
            }

            // Clear cart and applied coupon
            Cart::where('cart_token', $cartToken)->delete();
            session()->forget('applied_coupon');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'data' => [
                    'order_number' => $order->order_number,
                    'redirect_url' => route('order.success', ['order' => $order->order_number])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('COD Order processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order. Please try again.'
            ], 500);
        }
    }

    /**
     * Process online payment with PhonePe
     */
    private function processOnlinePayment(
        string $cartToken,
        $cartItems,
        array $billingAddress,
        array $shippingAddress,
        float $subtotal,
        float $shipping,
        float $tax,
        float $total,
        string $email,
        ?string $notes,
        ?int $couponId = null,
        ?string $couponCode = null,
        float $couponDiscount = 0
    ): JsonResponse {
        try {
            // Create pending order first
            $order = Order::create([
                'user_id' => null, // Guest checkout
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'payment' => [
                    'method' => 'online',
                    'status' => 'pending'
                ],
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'total' => $total,
                'coupon_id' => $couponId,
                'coupon_code' => $couponCode,
                'coupon_discount' => $couponDiscount,
                'status' => 'pending',
                'customer_notes' => $notes
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->defaultVariant?->price ?? 0,
                    'product_name' => $cartItem->product->name,
                    'variant_name' => $cartItem->product->defaultVariant?->name ?? 'Default'
                ]);
            }

            // Initialize PhonePe payment
            $paymentData = $this->initializePhonePePayment($order, $total);

            if (!$paymentData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize payment. Please try again.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Redirecting to payment gateway...',
                'data' => [
                    'payment_url' => $paymentData['payment_url'],
                    'order_number' => $order->order_number
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Online payment processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Initialize PhonePe payment using official SDK
     */
    private function initializePhonePePayment(Order $order, float $amount): array
    {
        try {
            $phonePeService = new PhonePeService();
            
            // Generate merchant order ID
            $merchantOrderId = $order->order_number;
            
            // Convert amount to paise (PhonePe expects amount in paise)
            $amountInPaise = (int)($amount * 100);
            
            // Initiate payment using SDK
            $paymentResult = $phonePeService->initiatePayment(
                $merchantOrderId,
                $amountInPaise,
                route('phonepe.redirect')
            );

            if ($paymentResult['success']) {
                // Update order with payment details
                $order->update([
                    'payment' => array_merge($order->payment, [
                        'transaction_id' => $merchantOrderId,
                        'gateway' => 'phonepe',
                        'order_id' => $paymentResult['order_id'] ?? null,
                        'session_expiry' => $paymentResult['session_expiry'] ?? null
                    ])
                ]);

                return [
                    'success' => true,
                    'payment_url' => $paymentResult['payment_url']
                ];
            }

            Log::error('PhonePe payment initialization failed', $paymentResult);
            return ['success' => false];

        } catch (\Exception $e) {
            Log::error('PhonePe initialization error: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Handle PhonePe callback using API
     */
    public function phonePeCallback(Request $request): JsonResponse
    {
        try {
            $phonePeService = new PhonePeService();
            
            // Get callback parameters
            $responseBody = $request->input('response');
            $checksum = $request->header('X-VERIFY');

            if (!$responseBody || !$checksum) {
                Log::error('PhonePe callback missing required parameters');
                return response()->json(['success' => false], 400);
            }

            // Verify callback
            $callbackResult = $phonePeService->verifyCallback($responseBody, $checksum);

            if (!$callbackResult['success']) {
                Log::error('PhonePe callback verification failed', $callbackResult);
                return response()->json(['success' => false], 400);
            }

            $merchantOrderId = $callbackResult['merchant_order_id'];
            $transactionStatus = $callbackResult['transaction_status'];

            // Find order by merchant order ID (which is our order number)
            $order = Order::where('order_number', $merchantOrderId)->first();

            if (!$order) {
                Log::error('Order not found for merchant order ID: ' . $merchantOrderId);
                return response()->json(['success' => false], 404);
            }

            // Update order based on payment status
            switch ($transactionStatus) {
                case 'COMPLETED':
                    $order->update([
                        'status' => 'confirmed',
                        'payment' => array_merge($order->payment, [
                            'status' => 'completed',
                            'gateway_response' => $callbackResult
                        ])
                    ]);
                    break;
                    
                case 'PENDING':
                    $order->update([
                        'status' => 'pending',
                        'payment' => array_merge($order->payment, [
                            'status' => 'pending',
                            'gateway_response' => $callbackResult
                        ])
                    ]);
                    break;
                    
                case 'FAILED':
                case 'CANCELLED':
                default:
                    $order->update([
                        'status' => 'cancelled',
                        'payment' => array_merge($order->payment, [
                            'status' => 'failed',
                            'gateway_response' => $callbackResult
                        ])
                    ]);
                    break;
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('PhonePe callback error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Handle PhonePe redirect using SDK for status verification
     */
    public function phonePeRedirect(Request $request)
    {
        try {
            $merchantOrderId = $request->input('merchantOrderId') ?? $request->input('transactionId');
            
            if ($merchantOrderId) {
                $phonePeService = new PhonePeService();
                
                // Check order status using SDK
                $statusResult = $phonePeService->checkOrderStatus($merchantOrderId, true);
                
                if ($statusResult['success']) {
                    // Find order by merchant order ID (our order number)
                    $order = Order::where('order_number', $merchantOrderId)->first();
                    
                    if ($order) {
                        // Update order status based on PhonePe response
                        $paymentState = $statusResult['state'];
                        
                        switch ($paymentState) {
                            case 'COMPLETED':
                                $order->update([
                                    'status' => 'confirmed',
                                    'payment' => array_merge($order->payment, [
                                        'status' => 'completed',
                                        'gateway_response' => $statusResult
                                    ])
                                ]);
                                
                                // Clear cart and applied coupon on successful payment
                                $cartToken = $request->cookie('cart_token');
                                if ($cartToken) {
                                    Cart::where('cart_token', $cartToken)->delete();
                                }
                                session()->forget('applied_coupon');
                                
                                return redirect()->route('order.success', ['order' => $order->order_number]);
                                
                            case 'PENDING':
                                $order->update([
                                    'status' => 'pending',
                                    'payment' => array_merge($order->payment, [
                                        'status' => 'pending',
                                        'gateway_response' => $statusResult
                                    ])
                                ]);
                                
                                return redirect()->route('order.pending', ['order' => $order->order_number]);
                                
                            case 'FAILED':
                            case 'CANCELLED':
                            default:
                                $order->update([
                                    'status' => 'cancelled',
                                    'payment' => array_merge($order->payment, [
                                        'status' => 'failed',
                                        'gateway_response' => $statusResult
                                    ])
                                ]);
                                
                                return redirect()->route('order.failed', ['order' => $order->order_number]);
                        }
                    }
                }
            }

            return redirect()->route('cart')->with('error', 'Payment status could not be determined.');
            
        } catch (\Exception $e) {
            Log::error('PhonePe redirect handling error: ' . $e->getMessage());
            return redirect()->route('cart')->with('error', 'An error occurred while processing your payment.');
        }
    }

    /**
     * Show order success page
     */
    public function orderSuccess($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items.product')->first();

        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('order-success', compact('order'));
    }

    /**
     * Show order failed page
     */
    public function orderFailed($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items.product')->first();

        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('order-failed', compact('order'));
    }

    /**
     * Show order pending page
     */
    public function orderPending($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items.product')->first();

        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('order-pending', compact('order'));
    }

    /**
     * Check payment status via AJAX
     */
    public function checkPaymentStatus($orderNumber): JsonResponse
    {
        try {
            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // If payment is already completed or failed, return current status
            if (in_array($order->payment['status'] ?? '', ['completed', 'failed'])) {
                return response()->json([
                    'success' => true,
                    'status' => $order->payment['status'],
                    'order_status' => $order->status,
                    'redirect_url' => $order->payment['status'] === 'completed' 
                        ? route('order.success', $order->order_number)
                        : route('order.failed', $order->order_number)
                ]);
            }

            // Check with PhonePe for pending payments
            $phonePeService = new PhonePeService();
            $statusResult = $phonePeService->checkOrderStatus($orderNumber, true);

            if ($statusResult['success']) {
                $paymentState = $statusResult['state'];
                
                // Update order based on current status
                switch ($paymentState) {
                    case 'COMPLETED':
                        $order->update([
                            'status' => 'confirmed',
                            'payment' => array_merge($order->payment, [
                                'status' => 'completed',
                                'gateway_response' => $statusResult
                            ])
                        ]);
                        
                        // Clear cart and applied coupon on successful payment
                        $cartToken = request()->cookie('cart_token');
                        if ($cartToken) {
                            Cart::where('cart_token', $cartToken)->delete();
                        }
                        session()->forget('applied_coupon');
                        
                        return response()->json([
                            'success' => true,
                            'status' => 'completed',
                            'order_status' => 'confirmed',
                            'redirect_url' => route('order.success', $order->order_number)
                        ]);
                        
                    case 'FAILED':
                    case 'CANCELLED':
                        $order->update([
                            'status' => 'cancelled',
                            'payment' => array_merge($order->payment, [
                                'status' => 'failed',
                                'gateway_response' => $statusResult
                            ])
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'status' => 'failed',
                            'order_status' => 'cancelled',
                            'redirect_url' => route('order.failed', $order->order_number)
                        ]);
                        
                    case 'PENDING':
                    default:
                        return response()->json([
                            'success' => true,
                            'status' => 'pending',
                            'order_status' => 'pending',
                            'message' => 'Payment is still being processed'
                        ]);
                }
            }

            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => 'Unable to check payment status. Please try again.'
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking payment status'
            ], 500);
        }
    }
}
