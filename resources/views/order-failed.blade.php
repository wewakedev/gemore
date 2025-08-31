@extends('layouts.app')

@section('title', 'Payment Failed - Ge More Nutralife')

@section('additional_css')
    <style>
        /* Order Failed Page Styles */
        .order-failed-page {
            padding: 60px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 80vh;
        }

        .failed-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 50px;
            text-align: center;
            margin-bottom: 40px;
        }

        .failed-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: failedPulse 2s ease-in-out infinite;
        }

        .failed-icon i {
            font-size: 3rem;
            color: white;
        }

        @keyframes failedPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .failed-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #dc3545;
            margin-bottom: 15px;
        }

        .failed-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        .order-number {
            background: #f8f9fa;
            border: 2px dashed #dc3545;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            display: inline-block;
        }

        .order-number h4 {
            color: #dc3545;
            font-weight: 700;
            margin: 0;
            font-size: 1.3rem;
        }

        .failure-reasons {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }

        .failure-reasons h5 {
            color: #856404;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .failure-reasons ul {
            color: #856404;
            margin: 0;
            padding-left: 20px;
        }

        .failure-reasons li {
            margin-bottom: 5px;
        }

        .order-details {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .order-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
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

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
            color: white;
        }

        .support-info {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }

        .support-info h5 {
            color: #0c5460;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .support-info p {
            color: #0c5460;
            margin: 5px 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .failed-container {
                padding: 30px 20px;
            }

            .failed-title {
                font-size: 2rem;
            }

            .order-info {
                padding: 20px;
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
    <!-- Order Failed Page Content -->
    <section class="order-failed-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Failed Message -->
                    <div class="failed-container">
                        <div class="failed-icon">
                            <i class="fas fa-times"></i>
                        </div>
                        <h1 class="failed-title">Payment Failed</h1>
                        <p class="failed-subtitle">We're sorry, but your payment could not be processed at this time.</p>
                        
                        <div class="order-number">
                            <h4>Order Number: {{ $order->order_number }}</h4>
                        </div>

                        <div class="failure-reasons">
                            <h5><i class="fas fa-exclamation-triangle"></i> Possible Reasons for Payment Failure:</h5>
                            <ul>
                                <li>Insufficient funds in your account</li>
                                <li>Network connectivity issues</li>
                                <li>Incorrect payment details</li>
                                <li>Bank server temporarily unavailable</li>
                                <li>Transaction timeout</li>
                            </ul>
                        </div>

                        <div class="support-info">
                            <h5><i class="fas fa-headset"></i> Need Help?</h5>
                            <p><strong>Customer Support:</strong> +91-XXXXXXXXXX</p>
                            <p><strong>Email:</strong> support@gemorenutralife.com</p>
                            <p><strong>Support Hours:</strong> Monday - Saturday, 9:00 AM - 6:00 PM</p>
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
                                <span class="info-value" style="color: #dc3545; font-weight: 600;">{{ $order->status_display }}</span>
                            </div>
                            @if($order->coupon_discount > 0)
                                <div class="info-row">
                                    <span class="info-label">Coupon Applied:</span>
                                    <span class="info-value">{{ $order->coupon_code }} (-₹{{ number_format($order->coupon_discount, 2) }})</span>
                                </div>
                            @endif
                            <div class="info-row">
                                <span class="info-label">Order Total:</span>
                                <span class="info-value" style="font-weight: 600;">₹{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="{{ route('cart') }}" class="btn btn-outline">
                            <i class="fas fa-shopping-cart"></i>
                            Back to Cart
                        </a>
                        <button onclick="retryPayment()" class="btn btn-danger">
                            <i class="fas fa-redo"></i>
                            Retry Payment
                        </button>
                        <a href="{{ route('store') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i>
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('additional_js')
    <script>
        function retryPayment() {
            // Show confirmation dialog
            if (confirm('Would you like to retry the payment for this order?')) {
                // Redirect to checkout with the same order details
                // You might want to implement a retry mechanism here
                window.location.href = '{{ route("checkout") }}';
            }
        }

        // Auto-focus on retry button for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Optional: You can add any additional JavaScript functionality here
            console.log('Payment failed page loaded');
        });
    </script>
@endsection
