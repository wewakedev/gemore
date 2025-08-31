@extends('layouts.app')

@section('title', 'Shopping Cart - Ge More Nutralife')

@section('additional_css')
    <style>
        /* Cart Page Styles */
        .cart-page {
            padding: 60px 0;
            background: #f8f9fa;
            min-height: 80vh;
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .page-header h1 i {
            color: #8b0000;
            margin-right: 15px;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .cart-items-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 25px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
        }

        .cart-item:hover {
            background: #f8f9fa;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex-grow: 1;
            margin-right: 20px;
        }

        .item-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-variant {
            color: #8b0000;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .item-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .original-price {
            color: #999;
            text-decoration: line-through;
            margin-right: 10px;
            font-size: 0.95rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            margin: 0 20px;
        }

        .quantity-btn {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: #8b0000;
            color: white;
            border-color: #8b0000;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            border: none;
            font-weight: 600;
            margin: 0 10px;
            font-size: 1rem;
        }

        .item-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: #8b0000;
            margin-right: 20px;
            min-width: 100px;
            text-align: right;
        }

        .remove-item {
            background: #dc3545;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .remove-item:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .cart-summary {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            position: sticky;
            top: 100px;
        }

        .cart-summary h4 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .summary-row span:first-child {
            color: #666;
        }

        .summary-row span:last-child {
            font-weight: 600;
            color: #333;
        }

        .total-row {
            border-top: 2px solid #eee;
            padding-top: 15px;
            margin-top: 20px;
            font-size: 1.2rem;
        }

        .total-row span {
            font-weight: 700;
            color: #333;
        }

        .coupon-section {
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .coupon-section h5 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .coupon-input {
            display: flex;
            gap: 10px;
        }

        .coupon-input input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .coupon-input input:focus {
            border-color: #8b0000;
            outline: none;
        }

        .coupon-input button {
            padding: 12px 20px;
            background: #8b0000;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .coupon-input button:hover {
            background: #a52a2a;
        }

        .coupon-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.9rem;
            display: none;
        }

        .coupon-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .coupon-message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .discount-row {
            color: #28a745;
        }

        .cart-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-outline {
            background: transparent;
            color: #666;
            border: 2px solid #dee2e6;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .btn-primary.btn-lg {
            background: linear-gradient(135deg, #8b0000, #a52a2a);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary.btn-lg:hover {
            background: linear-gradient(135deg, #a52a2a, #b22222);
            transform: translateY(-2px);
        }

        .empty-cart {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .empty-cart-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-cart h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 15px;
        }

        .empty-cart p {
            color: #666;
            margin-bottom: 30px;
        }

        .empty-cart .btn {
            background: linear-gradient(135deg, #8b0000, #a52a2a);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .empty-cart .btn:hover {
            background: linear-gradient(135deg, #a52a2a, #b22222);
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }

            .item-image {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .item-details {
                margin-right: 0;
                margin-bottom: 15px;
                width: 100%;
            }

            .quantity-controls {
                margin: 15px 0;
            }

            .item-total {
                margin-right: 0;
                text-align: left;
            }

            .coupon-input {
                flex-direction: column;
            }
        }
        
        /* Coupon Styles */
        .coupon-section {
            margin: 1rem 0;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .coupon-input {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .coupon-input input {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .applied-coupon {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }
        
        .coupon-info .coupon-name {
            font-weight: bold;
            color: #155724;
        }
        
        .coupon-info .coupon-code {
            font-size: 0.9em;
            color: #6c757d;
            margin-left: 0.5rem;
        }
        
        .coupon-message {
            padding: 0.5rem;
            border-radius: 4px;
            margin-top: 0.5rem;
            display: none;
        }
        
        .coupon-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .coupon-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .discount-row {
            color: #28a745;
        }
        
        .free-delivery-info {
            margin: 0.5rem 0;
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <!-- Cart Page Content -->
    <section class="cart-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="page-header">
                        <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
                        <p>Review your items and proceed to checkout</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Cart Items -->
                    <div class="cart-items-container" id="cart-items-container"
                        style="{{ $cartItems->count() > 0 ? '' : 'display: none;' }}">
                        @if ($cartItems->count() > 0)
                            @foreach ($cartItems as $item)
                                <div class="cart-item" data-product-id="{{ $item->product_id }}" data-variant-id="{{ $item->product_variant_id }}">
                                    <div class="item-image">
                                        <img src="{{ $item->product->images && count($item->product->images) > 0 ? asset('images/' . $item->product->images[0]) : asset('images/placeholder.svg') }}"
                                            alt="{{ $item->product->name }}" />
                                    </div>
                                    <div class="item-details">
                                        <div class="item-name">{{ $item->product->name }}</div>
                                        <div class="item-variant">
                                            {{ $item->productVariant ? $item->productVariant->name : ($item->product->defaultVariant ? $item->product->defaultVariant->name : 'Default') }}
                                        </div>
                                        <div class="item-price">
                                            ₹{{ $item->productVariant ? $item->productVariant->price : ($item->product->defaultVariant ? $item->product->defaultVariant->price : '0.00') }}
                                        </div>
                                    </div>
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateQuantity({{ $item->product_id }}, -1, {{ $item->product_variant_id ? $item->product_variant_id : 'null' }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-input" value="{{ $item->quantity }}"
                                            min="1" onchange="setQuantity({{ $item->product_id }}, this.value, {{ $item->product_variant_id ? $item->product_variant_id : 'null' }})">
                                        <button class="quantity-btn" onclick="updateQuantity({{ $item->product_id }}, 1, {{ $item->product_variant_id ? $item->product_variant_id : 'null' }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="item-total">
                                        ₹{{ ($item->productVariant ? $item->productVariant->price : ($item->product->defaultVariant ? $item->product->defaultVariant->price : 0)) * $item->quantity }}
                                    </div>
                                    <button class="remove-item" onclick="removeFromCart({{ $item->product_id }}, {{ $item->product_variant_id ? $item->product_variant_id : 'null' }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Empty Cart -->
                    <div class="empty-cart" id="empty-cart" style="{{ $cartItems->count() > 0 ? 'display: none;' : '' }}">
                        <div class="text-center">
                            <i class="fas fa-shopping-cart empty-cart-icon"></i>
                            <h3>Your cart is empty</h3>
                            <p>Looks like you haven't added any items to your cart yet.</p>
                            <a href="{{ route('store') }}" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Cart Summary -->
                    <div class="cart-summary" id="cart-summary" style="{{ $cartItems->count() > 0 ? '' : 'display: none;' }}">
                        <h4>Order Summary</h4>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal">₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span id="cart-shipping">{{ $total >= config('app.free_delivery_order_amount', 5000) ? 'Free' : '₹100.00' }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (18% GST):</span>
                            <span id="cart-tax">₹{{ number_format($total * 0.18, 2) }}</span>
                        </div>
                        
                        <!-- Coupon Section -->
                        <div class="coupon-section" id="coupon-section">
                            <h5>Have a coupon?</h5>
                            <div class="coupon-input" id="coupon-input-section">
                                <input type="text" id="coupon-code" placeholder="Enter coupon code" maxlength="50" />
                                <button class="btn btn-secondary" id="apply-coupon-btn">Apply</button>
                            </div>
                            <div class="applied-coupon" id="applied-coupon-section" style="display: none;">
                                <div class="coupon-info">
                                    <span class="coupon-name" id="applied-coupon-name"></span>
                                    <span class="coupon-code" id="applied-coupon-code"></span>
                                </div>
                                <button class="btn btn-sm btn-outline" id="remove-coupon-btn">Remove</button>
                            </div>
                            <div class="coupon-message" id="coupon-message"></div>
                        </div>
                        
                        <div class="summary-row discount-row" id="discount-row" style="display: none;">
                            <span>Discount:</span>
                            <span id="cart-discount" class="text-success">-₹0.00</span>
                        </div>
                        
                        <hr />
                        <div class="summary-row total-row">
                            <span><strong>Total:</strong></span>
                            <span><strong id="cart-total">₹{{ number_format($total + ($total >= config('app.free_delivery_order_amount', 5000) ? 0 : 100) + $total * 0.18, 2) }}</strong></span>
                        </div>
                        
                        <div class="free-delivery-info" id="free-delivery-info">
                            <small class="text-muted">
                                <i class="fa fa-truck"></i>
                                @if($total >= config('app.free_delivery_order_amount', 5000))
                                    You qualify for free delivery!
                                @else
                                    Add ₹{{ number_format(config('app.free_delivery_order_amount', 5000) - $total, 2) }} more for free delivery
                                @endif
                            </small>
                        </div>
                        
                        <div class="cart-actions">
                            <button class="btn btn-outline" id="clear-cart-btn">Clear Cart</button>
                            <button class="btn btn-primary btn-lg" id="checkout-btn">Proceed to Checkout</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('additional_js')
    <script src="{{ asset('js/cart-functions.js') }}"></script>
    <script>
        // Cart functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize coupon state from server data or localStorage
            initializeCouponState();
            
            // Event listeners
            document.getElementById('checkout-btn').addEventListener('click', proceedToCheckout);
            document.getElementById('apply-coupon-btn').addEventListener('click', applyCoupon);
            document.getElementById('remove-coupon-btn').addEventListener('click', removeCoupon);
            
            // Allow Enter key to apply coupon
            document.getElementById('coupon-code').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyCoupon();
                }
            });
            document.getElementById('clear-cart-btn').addEventListener('click', clearCart);
            
            // Checkout modal event listeners
            document.getElementById('close-checkout').addEventListener('click', hideCheckout);
            document.getElementById('back-to-cart').addEventListener('click', hideCheckout);
        });

        // Note: refreshCartPage function removed - now handled by CartFunctions.refreshCartState()

        // Function to update cart display with new data
        function updateCartDisplay(cartData) {
            const cartItemsContainer = document.getElementById('cart-items-container');
            const emptyCart = document.getElementById('empty-cart');
            const cartSummary = document.getElementById('cart-summary');

            if (cartData.items.length === 0) {
                cartItemsContainer.style.display = 'none';
                emptyCart.style.display = 'block';
                cartSummary.style.display = 'none';
            } else {
                cartItemsContainer.style.display = 'block';
                emptyCart.style.display = 'none';
                cartSummary.style.display = 'block';

                // Update individual cart items
                cartData.items.forEach(item => {
                    // Use both product_id and variant_id to find the specific cart item
                    const variantSelector = item.product_variant_id ? `[data-variant-id="${item.product_variant_id}"]` : '[data-variant-id=""], [data-variant-id="null"]';
                    const cartItem = document.querySelector(`[data-product-id="${item.product_id}"]${variantSelector}`);
                    if (cartItem) {
                        // Update quantity input
                        const quantityInput = cartItem.querySelector('.quantity-input');
                        if (quantityInput) {
                            quantityInput.value = item.quantity;
                        }

                        // Update item total using the correct variant price
                        const itemTotal = cartItem.querySelector('.item-total');
                        if (itemTotal) {
                            const price = item.product_variant?.price || item.product.default_variant?.price || item.product.defaultVariant?.price || 0;
                            const total = price * item.quantity;
                            itemTotal.textContent = `₹${total.toFixed(2)}`;
                        }
                    }
                });

                // Remove items that are no longer in cart
                const currentItems = document.querySelectorAll('.cart-item[data-product-id]');
                currentItems.forEach(item => {
                    const productId = item.getAttribute('data-product-id');
                    const variantId = item.getAttribute('data-variant-id');
                    const exists = cartData.items.some(cartItem => 
                        cartItem.product_id == productId && 
                        (cartItem.product_variant_id == variantId || (!cartItem.product_variant_id && (!variantId || variantId === 'null')))
                    );
                    if (!exists) {
                        item.remove();
                    }
                });
            }

            // Update totals
            updateCartTotals(cartData);
        }

        // Function to update cart totals using API response
        function updateCartTotals(cartData) {
            const totals = cartData.totals || {};
            
            // Update all total fields from API response
            document.getElementById('cart-subtotal').textContent = `₹${(totals.subtotal || 0).toFixed(2)}`;
            document.getElementById('cart-shipping').textContent = totals.shipping === 0 ? 'Free' : `₹${(totals.shipping || 0).toFixed(2)}`;
            document.getElementById('cart-tax').textContent = `₹${(totals.tax || 0).toFixed(2)}`;
            document.getElementById('cart-total').textContent = `₹${(totals.total || 0).toFixed(2)}`;
            
            // Update discount row
            const discountRow = document.getElementById('discount-row');
            if (totals.discount && totals.discount > 0) {
                document.getElementById('cart-discount').textContent = `-₹${totals.discount.toFixed(2)}`;
                discountRow.style.display = 'flex';
            } else {
                discountRow.style.display = 'none';
            }
            
            // Update free delivery info
            updateFreeDeliveryInfo(totals);
        }

        // Function to update free delivery information
        function updateFreeDeliveryInfo(totals) {
            const freeDeliveryInfo = document.getElementById('free-delivery-info');
            const freeDeliveryAmount = totals.free_delivery_amount || 5000;
            const currentSubtotal = totals.discounted_subtotal || totals.subtotal || 0;
            
            if (currentSubtotal >= freeDeliveryAmount) {
                freeDeliveryInfo.innerHTML = '<small class="text-muted"><i class="fa fa-truck"></i> You qualify for free delivery!</small>';
            } else {
                const remaining = freeDeliveryAmount - currentSubtotal;
                freeDeliveryInfo.innerHTML = `<small class="text-muted"><i class="fa fa-truck"></i> Add ₹${remaining.toFixed(2)} more for free delivery</small>`;
            }
        }

        // Function to clear entire cart
        function clearCart() {
            if (confirm('Are you sure you want to clear your entire cart?')) {
                fetch('/cart/clear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            CartFunctions.showNotification(data.message, 'success');
                            CartFunctions.refreshCartState();
                        } else {
                            CartFunctions.showNotification(data.message || 'Error clearing cart', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error clearing cart:', error);
                        CartFunctions.showNotification('Error clearing cart', 'error');
                    });
            }
        }

        // Coupon functionality
        let appliedCoupon = null;

        function initializeCouponState() {
            // Check if server provided coupon data
            @if(session('applied_coupon'))
                const serverCoupon = @json(session('applied_coupon'));
                if (serverCoupon) {
                    appliedCoupon = serverCoupon;
                    showAppliedCoupon(serverCoupon);
                    
                    // Store globally and in localStorage
                    if (typeof CartFunctions !== 'undefined') {
                        CartFunctions.globalAppliedCoupon = serverCoupon;
                        CartFunctions.sidebarAppliedCoupon = serverCoupon;
                        CartFunctions.saveCouponToStorage(serverCoupon);
                    }
                    
                    console.log('Restored coupon from server session:', serverCoupon);
                    return;
                }
            @endif
            
            // No server coupon, check localStorage and validate
            if (typeof CartFunctions !== 'undefined') {
                const storedCoupon = CartFunctions.loadCouponFromStorage();
                if (storedCoupon) {
                    console.log('Found stored coupon, validating:', storedCoupon);
                    CartFunctions.validateStoredCoupon(storedCoupon);
                }
            }
        }

        function applyCoupon() {
            const couponCode = document.getElementById('coupon-code').value.trim();
            const applyBtn = document.getElementById('apply-coupon-btn');

            if (!couponCode) {
                showCouponMessage('Please enter a coupon code', 'error');
                return;
            }

            // Disable button and show loading
            applyBtn.disabled = true;
            applyBtn.textContent = 'Applying...';
            clearCouponMessage();

            fetch('/coupon/apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    coupon_code: couponCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    appliedCoupon = data.data.coupon;
                    updateCartTotals({ totals: data.data.totals });
                    showAppliedCoupon(appliedCoupon);
                    showCouponMessage(`Coupon applied! You saved ₹${appliedCoupon.discount_amount.toFixed(2)}`, 'success');
                    
                    // Store globally for checkout modal and localStorage
                    if (typeof CartFunctions !== 'undefined') {
                        CartFunctions.globalAppliedCoupon = appliedCoupon;
                        CartFunctions.saveCouponToStorage(appliedCoupon);
                        CartFunctions.updateCheckoutTotals(data.data.totals);
                    }
                } else {
                    showCouponMessage(data.message || 'Invalid coupon code', 'error');
                }
            })
            .catch(error => {
                console.error('Error applying coupon:', error);
                showCouponMessage('An error occurred while applying the coupon', 'error');
            })
            .finally(() => {
                applyBtn.disabled = false;
                applyBtn.textContent = 'Apply';
            });
        }

        function removeCoupon() {
            const removeBtn = document.getElementById('remove-coupon-btn');
            
            // Disable button and show loading
            removeBtn.disabled = true;
            removeBtn.textContent = 'Removing...';
            clearCouponMessage();

            fetch('/coupon/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    appliedCoupon = null;
                    updateCartTotals({ totals: data.data.totals });
                    hideAppliedCoupon();
                    showCouponMessage('Coupon removed successfully', 'success');
                    
                    // Clear globally, localStorage and update checkout modal
                    if (typeof CartFunctions !== 'undefined') {
                        CartFunctions.globalAppliedCoupon = null;
                        CartFunctions.saveCouponToStorage(null);
                        CartFunctions.updateCheckoutTotals(data.data.totals);
                    }
                } else {
                    showCouponMessage(data.message || 'Error removing coupon', 'error');
                }
            })
            .catch(error => {
                console.error('Error removing coupon:', error);
                showCouponMessage('An error occurred while removing the coupon', 'error');
            })
            .finally(() => {
                removeBtn.disabled = false;
                removeBtn.textContent = 'Remove';
            });
        }

        function showAppliedCoupon(coupon) {
            document.getElementById('applied-coupon-name').textContent = coupon.name || 'Coupon';
            document.getElementById('applied-coupon-code').textContent = `(${coupon.code})`;
            document.getElementById('coupon-input-section').style.display = 'none';
            document.getElementById('applied-coupon-section').style.display = 'flex';
            document.getElementById('coupon-code').value = '';
        }

        function hideAppliedCoupon() {
            document.getElementById('coupon-input-section').style.display = 'flex';
            document.getElementById('applied-coupon-section').style.display = 'none';
        }

        function showCouponMessage(message, type) {
            const messageDiv = document.getElementById('coupon-message');
            messageDiv.textContent = message;
            messageDiv.className = `coupon-message ${type}`;
            messageDiv.style.display = 'block';
        }

        function clearCouponMessage() {
            const messageDiv = document.getElementById('coupon-message');
            messageDiv.textContent = '';
            messageDiv.className = 'coupon-message';
            messageDiv.style.display = 'none';
        }

        function proceedToCheckout() {
            const checkoutModal = document.getElementById("checkout-modal");
            checkoutModal.classList.add("active");
            
            // Update checkout modal with current cart data
            if (typeof CartFunctions !== 'undefined') {
                CartFunctions.updateCheckoutForm();
            }
        }

        function hideCheckout() {
            const checkoutModal = document.getElementById("checkout-modal");
            checkoutModal.classList.remove("active");
        }
    </script>
@endsection
