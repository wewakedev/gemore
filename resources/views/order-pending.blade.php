@extends('layouts.app')

@section('title', 'Payment Pending - Ge More Nutralife')

@section('additional_css')
    <style>
        /* Order Pending Page Styles */
        .order-pending-page {
            padding: 60px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 80vh;
        }

        .pending-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 50px;
            text-align: center;
            margin-bottom: 40px;
        }

        .pending-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }

        .pending-icon i {
            font-size: 50px;
            color: white;
        }

        .pending-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 15px;
        }

        .pending-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .order-number {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            border-radius: 15px;
            padding: 20px;
            margin: 30px 0;
        }

        .order-number h4 {
            color: #856404;
            font-weight: 600;
            margin: 0;
        }

        .pending-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
        }

        .pending-info h5 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .pending-info h5 i {
            margin-right: 10px;
            color: #ffc107;
        }

        .pending-info ul {
            margin: 0;
            padding-left: 20px;
        }

        .pending-info li {
            margin-bottom: 8px;
            color: #6c757d;
        }

        .action-buttons {
            margin-top: 40px;
        }

        .btn-check-status {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s ease;
        }

        .btn-check-status:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 123, 255, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-continue-shopping {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s ease;
        }

        .btn-continue-shopping:hover {
            background: #6c757d;
            color: white;
            text-decoration: none;
        }

        .order-details {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .order-details h3 {
            color: #343a40;
            font-weight: 700;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            margin-right: 20px;
            object-fit: cover;
        }

        .item-details h5 {
            color: #343a40;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-details p {
            color: #6c757d;
            margin: 0;
        }

        .item-price {
            margin-left: auto;
            text-align: right;
        }

        .item-price .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #28a745;
        }

        .order-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }

        .summary-row.total {
            border-top: 2px solid #dee2e6;
            padding-top: 15px;
            margin-top: 15px;
            font-weight: 700;
            font-size: 1.2rem;
            color: #343a40;
        }

        .support-info {
            background: #e3f2fd;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }

        .support-info h5 {
            color: #1976d2;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .support-info h5 i {
            margin-right: 10px;
        }

        .support-info p {
            margin-bottom: 8px;
            color: #424242;
        }

        @media (max-width: 768px) {
            .pending-container {
                padding: 30px 20px;
            }

            .pending-title {
                font-size: 2rem;
            }

            .order-details {
                padding: 25px 20px;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
            }

            .item-image {
                margin: 0 0 15px 0;
            }

            .item-price {
                margin: 15px 0 0 0;
            }
        }
    </style>
@endsection

@section('content')
<div class="order-pending-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Pending Message -->
                <div class="pending-container">
                    <div class="pending-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h1 class="pending-title">Payment Pending</h1>
                    <p class="pending-subtitle">Your payment is being processed. Please wait while we confirm your transaction.</p>
                    
                    <div class="order-number">
                        <h4>Order Number: {{ $order->order_number }}</h4>
                    </div>

                    <div class="pending-info">
                        <h5><i class="fas fa-info-circle"></i> What happens next?</h5>
                        <ul>
                            <li>Your payment is currently being processed by the payment gateway</li>
                            <li>This usually takes 2-5 minutes to complete</li>
                            <li>You will receive an email confirmation once payment is successful</li>
                            <li>If payment fails, the amount will be refunded automatically</li>
                            <li>You can check your payment status using the button below</li>
                        </ul>
                    </div>

                    <div class="support-info">
                        <h5><i class="fas fa-headset"></i> Need Help?</h5>
                        <p><strong>Customer Support:</strong> +91-XXXXXXXXXX</p>
                        <p><strong>Email:</strong> support@gemorenutralife.com</p>
                        <p><strong>Support Hours:</strong> Monday - Saturday, 9:00 AM - 6:00 PM</p>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn-check-status">
                            <i class="fas fa-sync-alt"></i> Check Payment Status
                        </button>
                        <a href="{{ route('store') }}" class="btn-continue-shopping">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="order-details">
                    <h3><i class="fas fa-receipt"></i> Order Details</h3>
                    
                    @foreach($order->items as $item)
                    <div class="order-item">
                        <img src="{{ $item->product->image_url ?? '/images/placeholder.svg' }}" alt="{{ $item->product_name }}" class="item-image">
                        <div class="item-details">
                            <h5>{{ $item->product_name }}</h5>
                            <p>Variant: {{ $item->variant_name }}</p>
                            <p>Quantity: {{ $item->quantity }}</p>
                        </div>
                        <div class="item-price">
                            <div class="price">₹{{ number_format($item->price * $item->quantity, 2) }}</div>
                        </div>
                    </div>
                    @endforeach

                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>₹{{ number_format($order->shipping, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax:</span>
                            <span>₹{{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span>₹{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh payment status every 30 seconds
let statusCheckInterval = setInterval(function() {
    checkPaymentStatus();
}, 30000); // Check every 30 seconds

// Check payment status function
function checkPaymentStatus() {
    fetch('{{ route("order.status", $order->order_number) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'completed') {
                    // Payment successful - redirect to success page
                    window.location.href = data.redirect_url;
                } else if (data.status === 'failed') {
                    // Payment failed - redirect to failure page
                    window.location.href = data.redirect_url;
                } else {
                    // Still pending - update status message if needed
                    console.log('Payment still pending: ' + (data.message || 'Processing...'));
                }
            }
        })
        .catch(error => {
            console.log('Error checking payment status:', error);
        });
}

// Manual status check button
document.addEventListener('DOMContentLoaded', function() {
    const checkStatusBtn = document.querySelector('.btn-check-status');
    if (checkStatusBtn) {
        checkStatusBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
            this.disabled = true;
            
            checkPaymentStatus();
            
            // Reset button after 3 seconds
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 3000);
        });
    }
});

// Stop checking after 15 minutes
setTimeout(function() {
    clearInterval(statusCheckInterval);
    console.log('Payment status checking stopped after 15 minutes');
}, 900000); // 15 minutes
</script>
@endsection
