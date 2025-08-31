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
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
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
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
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
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
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
                <div class="cart-items-container" id="cart-items-container">
                    <!-- Cart items will be loaded here -->
                </div>

                <!-- Empty Cart -->
                <div class="empty-cart" id="empty-cart" style="display: none">
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
                <div class="cart-summary">
                    <h4>Order Summary</h4>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">₹0</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="cart-shipping">₹0</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax:</span>
                        <span id="cart-tax">₹0</span>
                    </div>
                    <div class="coupon-section">
                        <h5>Have a coupon?</h5>
                        <div class="coupon-input">
                            <input type="text" id="coupon-code" placeholder="Enter coupon code" />
                            <button class="btn btn-secondary" id="apply-coupon">Apply</button>
                        </div>
                        <div class="coupon-message" id="coupon-message"></div>
                    </div>
                    <div class="summary-row discount-row" id="discount-row" style="display: none">
                        <span>Discount:</span>
                        <span id="cart-discount">-₹0</span>
                    </div>
                    <hr />
                    <div class="summary-row total-row">
                        <span><strong>Total:</strong></span>
                        <span><strong id="cart-total">₹0</strong></span>
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
<script>
// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    
    // Event listeners
    document.getElementById('clear-cart-btn').addEventListener('click', clearCart);
    document.getElementById('checkout-btn').addEventListener('click', proceedToCheckout);
    document.getElementById('apply-coupon').addEventListener('click', applyCoupon);
});

function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartContainer = document.getElementById('cart-items-container');
    const emptyCart = document.getElementById('empty-cart');
    
    if (cart.length === 0) {
        cartContainer.style.display = 'none';
        emptyCart.style.display = 'block';
        updateCartSummary([]);
        return;
    }
    
    cartContainer.style.display = 'block';
    emptyCart.style.display = 'none';
    
    cartContainer.innerHTML = cart.map(item => `
        <div class="cart-item" data-product-id="${item.productId}" data-variant-id="${item.variantId}">
            <div class="item-image">
                <img src="${item.image}" alt="${item.name}" />
            </div>
            <div class="item-details">
                <div class="item-name">${item.name}</div>
                <div class="item-variant">${item.variant}</div>
                <div class="item-price">
                    ${item.originalPrice && item.originalPrice > item.price ? 
                        `<span class="original-price">₹${item.originalPrice}</span>` : ''}
                    ₹${item.price}
                </div>
            </div>
            <div class="quantity-controls">
                <button class="quantity-btn" onclick="updateQuantity(${item.productId}, ${item.variantId}, -1)">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" class="quantity-input" value="${item.quantity}" min="1" 
                       onchange="setQuantity(${item.productId}, ${item.variantId}, this.value)">
                <button class="quantity-btn" onclick="updateQuantity(${item.productId}, ${item.variantId}, 1)">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="item-total">₹${(item.price * item.quantity).toFixed(2)}</div>
            <button class="remove-item" onclick="removeFromCart(${item.productId}, ${item.variantId})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');
    
    updateCartSummary(cart);
}

function updateQuantity(productId, variantId, change) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const itemIndex = cart.findIndex(item => item.productId === productId && item.variantId === variantId);
    
    if (itemIndex !== -1) {
        cart[itemIndex].quantity += change;
        if (cart[itemIndex].quantity <= 0) {
            cart.splice(itemIndex, 1);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    }
}

function setQuantity(productId, variantId, quantity) {
    quantity = parseInt(quantity);
    if (quantity <= 0) return;
    
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const itemIndex = cart.findIndex(item => item.productId === productId && item.variantId === variantId);
    
    if (itemIndex !== -1) {
        cart[itemIndex].quantity = quantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    }
}

function removeFromCart(productId, variantId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart = cart.filter(item => !(item.productId === productId && item.variantId === variantId));
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
}

function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
        localStorage.removeItem('cart');
        loadCart();
    }
}

function updateCartSummary(cart) {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = subtotal > 1000 ? 0 : 100; // Free shipping over ₹1000
    const tax = subtotal * 0.18; // 18% GST
    const total = subtotal + shipping + tax;
    
    document.getElementById('cart-subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('cart-shipping').textContent = shipping === 0 ? 'Free' : `₹${shipping.toFixed(2)}`;
    document.getElementById('cart-tax').textContent = `₹${tax.toFixed(2)}`;
    document.getElementById('cart-total').textContent = `₹${total.toFixed(2)}`;
}

function applyCoupon() {
    const couponCode = document.getElementById('coupon-code').value.trim();
    const messageDiv = document.getElementById('coupon-message');
    
    if (!couponCode) {
        showCouponMessage('Please enter a coupon code', 'error');
        return;
    }
    
    // Simulate coupon validation
    const validCoupons = {
        'WELCOME10': { discount: 10, type: 'percentage' },
        'SAVE100': { discount: 100, type: 'fixed' },
        'NEWUSER': { discount: 15, type: 'percentage' }
    };
    
    const coupon = validCoupons[couponCode.toUpperCase()];
    
    if (coupon) {
        showCouponMessage(`Coupon applied! You saved ${coupon.type === 'percentage' ? coupon.discount + '%' : '₹' + coupon.discount}`, 'success');
        document.getElementById('discount-row').style.display = 'flex';
        // Apply discount logic here
    } else {
        showCouponMessage('Invalid coupon code', 'error');
    }
}

function showCouponMessage(message, type) {
    const messageDiv = document.getElementById('coupon-message');
    messageDiv.textContent = message;
    messageDiv.className = `coupon-message ${type}`;
}

function proceedToCheckout() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }
    
    // Redirect to checkout page
    window.location.href = '/checkout';
}
</script>
@endsection 