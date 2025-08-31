@extends('layouts.app')

@section('title', 'Checkout - Ge More Nutralife')

@section('additional_css')
<style>
/* Checkout Page Styles */
.checkout-page {
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

.checkout-steps {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
}

.step {
    display: flex;
    align-items: center;
    margin: 0 20px;
    color: #999;
}

.step.active {
    color: #8b0000;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #dee2e6;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 10px;
}

.step.active .step-number {
    background: #8b0000;
    color: white;
}

.checkout-form {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    padding: 40px;
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: block;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #8b0000;
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.payment-method {
    border: 2px solid #dee2e6;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method:hover {
    border-color: #8b0000;
    background: #f8f9fa;
}

.payment-method.selected {
    border-color: #8b0000;
    background: rgba(139, 0, 0, 0.05);
}

.payment-method i {
    font-size: 2rem;
    color: #8b0000;
    margin-bottom: 10px;
}

.payment-method h5 {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.payment-method p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

.order-summary {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    padding: 30px;
    position: sticky;
    top: 100px;
}

.order-summary h4 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 25px;
    text-align: center;
}

.order-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    margin-right: 15px;
    flex-shrink: 0;
}

.order-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.order-item-details {
    flex-grow: 1;
}

.order-item-name {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
    margin-bottom: 3px;
}

.order-item-variant {
    color: #8b0000;
    font-size: 0.85rem;
    margin-bottom: 3px;
}

.order-item-quantity {
    color: #666;
    font-size: 0.85rem;
}

.order-item-price {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
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

.place-order-btn {
    width: 100%;
    background: linear-gradient(135deg, #8b0000, #a52a2a);
    color: white;
    border: none;
    padding: 15px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 30px;
}

.place-order-btn:hover {
    background: linear-gradient(135deg, #a52a2a, #b22222);
    transform: translateY(-2px);
}

.place-order-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.security-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
    text-align: center;
}

.security-info i {
    font-size: 2rem;
    color: #28a745;
    margin-bottom: 10px;
}

.security-info h6 {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.security-info p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .checkout-steps {
        flex-direction: column;
        align-items: center;
    }
    
    .step {
        margin: 10px 0;
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .payment-methods {
        grid-template-columns: 1fr;
    }
    
    .checkout-form {
        padding: 25px;
    }
}
</style>
@endsection

@section('content')
<!-- Checkout Page Content -->
<section class="checkout-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1><i class="fas fa-credit-card"></i> Checkout</h1>
                    <div class="checkout-steps">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <span>Billing Info</span>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <span>Payment</span>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <span>Confirmation</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Checkout Form -->
                <form id="checkout-form" class="checkout-form">
                    @csrf
                    <!-- Billing Information -->
                    <div class="billing-section">
                        <h4 class="section-title">Billing Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="billing-name">Full Name *</label>
                                <input type="text" id="billing-name" name="billing-name" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-email">Email Address *</label>
                                <input type="email" id="billing-email" name="billing-email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="billing-phone">Phone Number *</label>
                                <input type="tel" id="billing-phone" name="billing-phone" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-alt-phone">Alternate Phone</label>
                                <input type="tel" id="billing-alt-phone" name="billing-alt-phone">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="billing-address">Address *</label>
                            <textarea id="billing-address" name="billing-address" rows="3" required></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="billing-city">City *</label>
                                <input type="text" id="billing-city" name="billing-city" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-state">State *</label>
                                <select id="billing-state" name="billing-state" required>
                                    <option value="">Select State</option>
                                    <option value="andhra-pradesh">Andhra Pradesh</option>
                                    <option value="arunachal-pradesh">Arunachal Pradesh</option>
                                    <option value="assam">Assam</option>
                                    <option value="bihar">Bihar</option>
                                    <option value="chhattisgarh">Chhattisgarh</option>
                                    <option value="goa">Goa</option>
                                    <option value="gujarat">Gujarat</option>
                                    <option value="haryana">Haryana</option>
                                    <option value="himachal-pradesh">Himachal Pradesh</option>
                                    <option value="jharkhand">Jharkhand</option>
                                    <option value="karnataka">Karnataka</option>
                                    <option value="kerala">Kerala</option>
                                    <option value="madhya-pradesh">Madhya Pradesh</option>
                                    <option value="maharashtra">Maharashtra</option>
                                    <option value="manipur">Manipur</option>
                                    <option value="meghalaya">Meghalaya</option>
                                    <option value="mizoram">Mizoram</option>
                                    <option value="nagaland">Nagaland</option>
                                    <option value="odisha">Odisha</option>
                                    <option value="punjab">Punjab</option>
                                    <option value="rajasthan">Rajasthan</option>
                                    <option value="sikkim">Sikkim</option>
                                    <option value="tamil-nadu">Tamil Nadu</option>
                                    <option value="telangana">Telangana</option>
                                    <option value="tripura">Tripura</option>
                                    <option value="uttar-pradesh">Uttar Pradesh</option>
                                    <option value="uttarakhand">Uttarakhand</option>
                                    <option value="west-bengal">West Bengal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="billing-pincode">Pincode *</label>
                                <input type="text" id="billing-pincode" name="billing-pincode" maxlength="6" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="payment-section">
                        <h4 class="section-title">Payment Method</h4>
                        <div class="payment-methods">
                            <div class="payment-method" data-method="cod">
                                <i class="fas fa-money-bill-wave"></i>
                                <h5>Cash on Delivery</h5>
                                <p>Pay when you receive</p>
                            </div>
                            <div class="payment-method" data-method="upi">
                                <i class="fab fa-google-pay"></i>
                                <h5>UPI Payment</h5>
                                <p>Google Pay, PhonePe, Paytm</p>
                            </div>
                            <div class="payment-method" data-method="card">
                                <i class="fas fa-credit-card"></i>
                                <h5>Credit/Debit Card</h5>
                                <p>Visa, MasterCard, RuPay</p>
                            </div>
                            <div class="payment-method" data-method="netbanking">
                                <i class="fas fa-university"></i>
                                <h5>Net Banking</h5>
                                <p>All major banks</p>
                            </div>
                        </div>
                        <input type="hidden" id="payment-method" name="payment-method" required>
                    </div>

                    <!-- Order Notes -->
                    <div class="notes-section">
                        <h4 class="section-title">Order Notes (Optional)</h4>
                        <div class="form-group">
                            <label for="order-notes">Special Instructions</label>
                            <textarea id="order-notes" name="order-notes" rows="3" placeholder="Any special instructions for delivery..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="order-summary">
                    <h4>Order Summary</h4>
                    <div id="order-items">
                        <!-- Order items will be loaded here -->
                    </div>
                    <div class="summary-section">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="order-subtotal">₹0</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span id="order-shipping">₹0</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (GST):</span>
                            <span id="order-tax">₹0</span>
                        </div>
                        <div class="summary-row total-row">
                            <span><strong>Total:</strong></span>
                            <span><strong id="order-total">₹0</strong></span>
                        </div>
                    </div>
                    <button type="submit" form="checkout-form" class="place-order-btn" id="place-order-btn">
                        <i class="fas fa-lock"></i> Place Order
                    </button>
                    
                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <h6>Secure Checkout</h6>
                        <p>Your payment information is encrypted and secure</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('additional_js')
<script>
// Checkout functionality
document.addEventListener('DOMContentLoaded', function() {
    loadOrderSummary();
    setupPaymentMethods();
    setupFormValidation();
});

function loadOrderSummary() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const orderItemsContainer = document.getElementById('order-items');
    
    if (cart.length === 0) {
        window.location.href = '/cart';
        return;
    }
    
    orderItemsContainer.innerHTML = cart.map(item => `
        <div class="order-item">
            <div class="order-item-image">
                <img src="${item.image}" alt="${item.name}" />
            </div>
            <div class="order-item-details">
                <div class="order-item-name">${item.name}</div>
                <div class="order-item-variant">${item.variant}</div>
                <div class="order-item-quantity">Qty: ${item.quantity}</div>
            </div>
            <div class="order-item-price">₹${(item.price * item.quantity).toFixed(2)}</div>
        </div>
    `).join('');
    
    updateOrderSummary(cart);
}

function updateOrderSummary(cart) {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = subtotal > 1000 ? 0 : 100; // Free shipping over ₹1000
    const tax = subtotal * 0.18; // 18% GST
    const total = subtotal + shipping + tax;
    
    document.getElementById('order-subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('order-shipping').textContent = shipping === 0 ? 'Free' : `₹${shipping.toFixed(2)}`;
    document.getElementById('order-tax').textContent = `₹${tax.toFixed(2)}`;
    document.getElementById('order-total').textContent = `₹${total.toFixed(2)}`;
}

function setupPaymentMethods() {
    const paymentMethods = document.querySelectorAll('.payment-method');
    const paymentMethodInput = document.getElementById('payment-method');
    
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove selected class from all methods
            paymentMethods.forEach(m => m.classList.remove('selected'));
            
            // Add selected class to clicked method
            this.classList.add('selected');
            
            // Set the payment method value
            paymentMethodInput.value = this.dataset.method;
        });
    });
}

function setupFormValidation() {
    const form = document.getElementById('checkout-form');
    const placeOrderBtn = document.getElementById('place-order-btn');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            placeOrder();
        }
    });
}

function validateForm() {
    const requiredFields = document.querySelectorAll('#checkout-form [required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#dc3545';
            isValid = false;
        } else {
            field.style.borderColor = '#dee2e6';
        }
    });
    
    // Validate payment method
    const paymentMethod = document.getElementById('payment-method').value;
    if (!paymentMethod) {
        alert('Please select a payment method');
        isValid = false;
    }
    
    // Validate pincode
    const pincode = document.getElementById('billing-pincode').value;
    if (pincode && !/^\d{6}$/.test(pincode)) {
        document.getElementById('billing-pincode').style.borderColor = '#dc3545';
        alert('Please enter a valid 6-digit pincode');
        isValid = false;
    }
    
    // Validate phone
    const phone = document.getElementById('billing-phone').value;
    if (phone && !/^\d{10}$/.test(phone)) {
        document.getElementById('billing-phone').style.borderColor = '#dc3545';
        alert('Please enter a valid 10-digit phone number');
        isValid = false;
    }
    
    return isValid;
}

function placeOrder() {
    const placeOrderBtn = document.getElementById('place-order-btn');
    const originalText = placeOrderBtn.innerHTML;
    
    // Disable button and show loading
    placeOrderBtn.disabled = true;
    placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    // Get form data
    const formData = new FormData(document.getElementById('checkout-form'));
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Prepare order data
    const orderData = {
        items: cart,
        billing: {
            name: formData.get('billing-name'),
            email: formData.get('billing-email'),
            phone: formData.get('billing-phone'),
            altPhone: formData.get('billing-alt-phone'),
            address: formData.get('billing-address'),
            city: formData.get('billing-city'),
            state: formData.get('billing-state'),
            pincode: formData.get('billing-pincode')
        },
        payment: {
            method: formData.get('payment-method')
        },
        notes: formData.get('order-notes')
    };
    
    // Simulate order processing
    setTimeout(() => {
        // Send order confirmation email
        fetch('/api/order-confirmation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                customerName: orderData.billing.name,
                customerEmail: orderData.billing.email,
                items: orderData.items,
                total: document.getElementById('order-total').textContent
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear cart
                localStorage.removeItem('cart');
                
                // Show success message
                alert('Order placed successfully! You will receive a confirmation email shortly.');
                
                // Redirect to home page
                window.location.href = '/';
            } else {
                throw new Error(data.error || 'Failed to place order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error placing your order. Please try again.');
        })
        .finally(() => {
            // Re-enable button
            placeOrderBtn.disabled = false;
            placeOrderBtn.innerHTML = originalText;
        });
    }, 2000); // Simulate processing time
}
</script>
@endsection 