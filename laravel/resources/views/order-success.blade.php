@extends('layouts.app')

@section('title', 'Order Placed Successfully - Ge More Nutralife')

@section('additional_css')
    <style>
        /* Order Success Page Styles */
        .order-success-page {
            padding: 60px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 80vh;
        }

        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 50px;
            text-align: center;
            margin-bottom: 40px;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: successPulse 2s ease-in-out infinite;
        }

        .success-icon i {
            font-size: 3rem;
            color: white;
        }

        @keyframes successPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .success-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .success-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        .order-number {
            background: #f8f9fa;
            border: 2px dashed #8b0000;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            display: inline-block;
        }

        .order-number h4 {
            color: #8b0000;
            font-weight: 700;
            margin: 0;
            font-size: 1.3rem;
        }

        .order-details {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .order-header {
            background: linear-gradient(135deg, #8b0000, #a52a2a);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .order-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .order-info {
            padding: 30px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #333;
        }

        .info-value {
            color: #666;
        }

        .order-items {
            padding: 0 30px 30px;
        }

        .order-items h4 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex-grow: 1;
        }

        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-variant {
            color: #8b0000;
            font-size: 0.9rem;
        }

        .item-quantity {
            color: #666;
            margin: 0 20px;
            font-weight: 500;
        }

        .item-price {
            font-weight: 600;
            color: #333;
        }

        .order-summary {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row.total {
            border-top: 2px solid #ddd;
            padding-top: 15px;
            margin-top: 15px;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b0000, #a52a2a);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #a52a2a, #b22222);
            transform: translateY(-2px);
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: #666;
            border: 2px solid #dee2e6;
        }

        .btn-outline:hover {
            background: #f8f9fa;
            border-color: #8b0000;
            color: #8b0000;
        }

        .delivery-info {
            background: #e8f5e8;
            border: 1px solid #28a745;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }

        .delivery-info h5 {
            color: #28a745;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .delivery-info p {
            color: #155724;
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .success-container {
                padding: 30px 20px;
            }

            .success-title {
                font-size: 2rem;
            }

            .order-info {
                padding: 20px;
            }

            .order-items {
                padding: 0 20px 20px;
            }

            .order-item {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }

            .item-image {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .item-quantity {
                margin: 10px 0;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Order Success Page Content -->
    <section class="order-success-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Success Message -->
                    <div class="success-container">
                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h1 class="success-title">Order Placed Successfully!</h1>
                        <p class="success-subtitle">Thank you for your order. We'll send you a confirmation email shortly.</p>
                        
                        <div class="order-number">
                            <h4>Order Number: {{ $order->order_number }}</h4>
                        </div>

                        <div class="delivery-info">
                            <h5><i class="fas fa-truck"></i> Estimated Delivery</h5>
                            <p>Your order will be delivered within 3-5 business days to your shipping address.</p>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="order-details">
                        <div class="order-header">
                            <h3>Order Details</h3>
                        </div>

                        <div class="order-info">
                            <div class="info-row">
                                <span class="info-label">Order Date:</span>
                                <span class="info-value">{{ $order->created_at->format('F d, Y') }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Payment Method:</span>
                                <span class="info-value">{{ ucfirst($order->payment['method']) === 'Cod' ? 'Cash on Delivery' : 'Online Payment' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Order Status:</span>
                                <span class="info-value">{{ $order->status_display }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Shipping Address:</span>
                                <span class="info-value">
                                    {{ $order->shipping_address['name'] }}<br>
                                    {{ $order->shipping_address['address'] }}<br>
                                    {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['pincode'] }}<br>
                                    Phone: {{ $order->shipping_address['phone'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="order-items">
                            <h4>Items Ordered ({{ $order->total_items }} items)</h4>
                            @foreach($order->items as $item)
                                <div class="order-item">
                                    <div class="item-image">
                                        <img src="{{ $item->product && $item->product->images && count($item->product->images) > 0 ? asset('images/' . $item->product->images[0]) : asset('images/placeholder.svg') }}" 
                                             alt="{{ $item->product_name }}" />
                                    </div>
                                    <div class="item-details">
                                        <div class="item-name">{{ $item->product_name }}</div>
                                        <div class="item-variant">
                                            {{ $item->variant_name ?? 'Default' }}
                                            @if($item->variant_size)
                                                - {{ $item->variant_size }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="item-quantity">Qty: {{ $item->quantity }}</div>
                                    <div class="item-price">₹{{ number_format($item->price, 2) }}</div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Summary -->
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>₹{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span>{{ $order->shipping > 0 ? '₹' . number_format($order->shipping, 2) : 'Free' }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax (GST):</span>
                                <span>₹{{ number_format($order->tax, 2) }}</span>
                            </div>
                            @if($order->coupon_discount > 0)
                                <div class="summary-row">
                                    <span>Discount ({{ $order->coupon_code }}):</span>
                                    <span>-₹{{ number_format($order->coupon_discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span>₹{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('store') }}" class="btn btn-outline">
                            <i class="fas fa-shopping-bag"></i>
                            Continue Shopping
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i>
                            Print Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('additional_js')
    <script>
        // Auto-refresh cart count after successful order
        document.addEventListener('DOMContentLoaded', function() {
            // Update cart count to 0 since order was placed
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = '0';
                cartCount.style.display = 'none';
            }
        });
    </script>
@endsection
