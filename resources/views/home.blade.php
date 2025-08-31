@extends('layouts.app')

@section('title', 'Ge More Nutralife - Premium Sports Supplements | Whey Protein, Pre-Workout & More')

@section('additional_css')
<style>
/* Featured Products Grid Styles */
.product_showcase {
    padding: 80px 0;
    background: #fff;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin: 40px 0;
}

.product-item {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.product-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.product-card {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-item:hover .product-image img {
    transform: scale(1.1);
}

.no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: #f8f9fa;
    color: #ccc;
    font-size: 3rem;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(139, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-item:hover .product-overlay {
    opacity: 1;
}

.btn-quick-view {
    background: white;
    color: #8b0000;
    border: none;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-quick-view:hover {
    background: #f8f9fa;
    transform: scale(1.05);
}

.product-badges {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 2;
}

.badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-right: 8px;
    margin-bottom: 5px;
}

.badge-featured {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.badge-sale {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
}

.wishlist-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.9);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    transition: all 0.3s ease;
}

.wishlist-btn:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.1);
}

.product-info {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    color: #8b0000;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.product-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.3;
}

.product-description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 15px;
    flex-grow: 1;
}

.product-variants {
    display: flex;
    gap: 8px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.variant-option {
    background: #f8f9fa;
    color: #333;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.variant-more {
    background: #8b0000;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.product-price {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.original-price {
    color: #999;
    text-decoration: line-through;
    margin-right: 10px;
    font-size: 1rem;
}

.current-price {
    color: #8b0000;
}

.product-actions {
    display: flex;
    gap: 10px;
}

.btn-add-to-cart, .btn-buy-now {
    flex: 1;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    font-size: 0.85rem;
}

.btn-add-to-cart {
    background: #f8f9fa;
    color: #333;
    border: 2px solid #dee2e6;
}

.btn-add-to-cart:hover {
    background: #8b0000;
    color: white;
    border-color: #8b0000;
}

.btn-buy-now {
    background: linear-gradient(135deg, #8b0000, #a52a2a);
    color: white;
    border: none;
}

.btn-buy-now:hover {
    background: linear-gradient(135deg, #a52a2a, #b22222);
    transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 992px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .product-actions {
        flex-direction: column;
    }
}
</style>
@endsection

@section('content')
<!-- Hero Section -->
<header class="hero-section custom-hero">
    <div class="container hero-flex">
        <div class="hero-left">
            <h1>Ge More Nutralife</h1>
            <div class="hero-details">
                <h2>100% pure and trusted supplements</h2>
                <p>Scientifically Manufactured and Tested</p>
                <p class="hero-contact">
                    For Delivery: <strong>+91 92117 98913</strong>
                </p>
            </div>
        </div>
        <div class="hero-center">
            <img
                src="{{ asset('images/product_fruit-removebg-preview.png') }}"
                alt="Ge More Nutralife Premium Pre-Workout Supplement - Fruit Punch Flavor"
                class="hero-product-img"
                width="400"
                height="400"
                loading="eager"
            />
        </div>
        <div class="hero-right">
            <div class="hero-product-title">Premium Quality Supplements</div>
            <p>GMP & FSSAI Certified</p>
            <a href="{{ route('store') }}" class="btn btn-primary shop-now-btn">
                <i class="fas fa-shopping-cart"></i>
                Shop Now
            </a>
        </div>
    </div>
</header>

<!-- Featured Products Section -->
<div id="products" class="product_showcase">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage text-center">
                    <h2>Featured Products</h2>
                    <p>Premium quality supplements for your fitness journey</p>
                </div>
            </div>
        </div>

        <!-- Featured Products Grid -->
        <div class="products-grid" id="featured-products-grid">
            @foreach($featuredProducts as $product)
            <div class="product-item">
                <div class="product-card">
                    <div class="product-image">
                        @if($product->first_image)
                        <img src="{{ asset('images/' . $product->first_image) }}" alt="{{ $product->name }}" />
                        @else
                        <div class="no-image">
                            <i class="fas fa-image"></i>
                        </div>
                        @endif
                        
                        <!-- Product Overlay -->
                        <div class="product-overlay">
                            <button class="btn btn-quick-view" onclick="quickView({{ $product->id }})">
                                <i class="fas fa-eye"></i> Quick View
                            </button>
                        </div>
                        
                        <!-- Product Badges -->
                        <div class="product-badges">
                            @if($product->is_featured)
                            <span class="badge badge-featured">Featured</span>
                            @endif
                            @if($product->discount_percentage > 0)
                            <span class="badge badge-sale">{{ $product->discount_percentage }}% Off</span>
                            @endif
                        </div>
                        
                        <!-- Wishlist Button -->
                        <button class="wishlist-btn" onclick="toggleWishlist({{ $product->id }})">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-category">{{ $product->category->name }}</div>
                        <h3 class="product-name">{{ $product->name }}</h3>
                        <p class="product-description">{{ Str::limit($product->description, 100) }}</p>
                        
                        @if($product->activeVariants->count() > 0)
                        <div class="product-variants">
                            @foreach($product->activeVariants->take(2) as $variant)
                            <span class="variant-option">{{ $variant->name }}</span>
                            @endforeach
                            @if($product->activeVariants->count() > 2)
                            <span class="variant-more">+{{ $product->activeVariants->count() - 2 }} more</span>
                            @endif
                        </div>
                        
                        <div class="product-price">
                            @php
                                $defaultVariant = $product->activeVariants->where('is_default', true)->first();
                                $minPrice = $product->activeVariants->min('price');
                            @endphp
                            @if($defaultVariant && $defaultVariant->original_price && $defaultVariant->original_price > $defaultVariant->price)
                            <span class="original-price">₹{{ number_format($defaultVariant->original_price) }}</span>
                            <span class="current-price">₹{{ number_format($defaultVariant->price) }}</span>
                            @else
                            <span class="current-price">₹{{ number_format($minPrice) }}</span>
                            @endif
                        </div>
                        @endif
                        
                        <div class="product-actions">
                            <button class="btn btn-add-to-cart" onclick="addToCart({{ $product->id }})">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="btn btn-buy-now" onclick="buyNow({{ $product->id }})">
                                <i class="fas fa-bolt"></i> Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- View More CTA -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="{{ route('store') }}" class="btn btn-primary btn-lg">
                    View All Products <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- About Section -->
<section id="about" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img
                    src="{{ asset('images/aboutgemore.jpg') }}"
                    alt="Ge More Nutralife - GMP and FSSAI certified sports supplements manufacturer with premium quality assurance"
                    class="img-fluid rounded shadow"
                    width="600"
                    height="400"
                    loading="lazy"
                />
            </div>
            <div class="col-md-6">
                <h2>About Ge More Nutralife</h2>
                <p>We are committed to providing premium quality sports supplements that help you achieve your fitness goals. Our products are GMP & FSSAI certified, ensuring the highest standards of quality and safety.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> GMP Certified Manufacturing</li>
                    <li><i class="fas fa-check text-success"></i> FSSAI Approved Products</li>
                    <li><i class="fas fa-check text-success"></i> Premium Quality Ingredients</li>
                    <li><i class="fas fa-check text-success"></i> Scientifically Formulated</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage text-center">
                    <h2>Get In Touch</h2>
                    <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                    <div class="contact-divider"></div>
                </div>
            </div>
        </div>

        <div class="row align-items-center">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-container">
                    <h3><i class="fas fa-paper-plane"></i> Send us a Message</h3>
                    <form id="contactForm" method="POST" action="/api/contact">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        <i class="fas fa-user"></i> Full Name *
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="name"
                                        name="name"
                                        placeholder="Enter your full name"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">
                                        <i class="fas fa-envelope"></i> Email Address *
                                    </label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="Enter your email"
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">
                                        <i class="fas fa-phone"></i> Phone Number
                                    </label>
                                    <input
                                        type="tel"
                                        class="form-control"
                                        id="phone"
                                        name="phone"
                                        placeholder="Enter your phone number"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject">
                                        <i class="fas fa-tag"></i> Subject
                                    </label>
                                    <select class="form-control" id="subject" name="subject">
                                        <option value="">Select a subject</option>
                                        <option value="Product Inquiry">Product Inquiry</option>
                                        <option value="Bulk Order">Bulk Order</option>
                                        <option value="General Question">General Question</option>
                                        <option value="Support">Support</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">
                                <i class="fas fa-comment"></i> Your Message *
                            </label>
                            <textarea
                                class="form-control"
                                id="message"
                                name="message"
                                rows="5"
                                placeholder="Tell us how we can help you..."
                                required
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            class="btn btn-primary btn-contact-primary"
                        >
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                    <div id="form-messages" class="mt-3" style="display: none"></div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="contact-info">
                    <h3><i class="fas fa-map-marker-alt"></i> Contact Information</h3>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email Us</h4>
                            <p>
                                <a href="mailto:info@gemorenutralife.com">info@gemorenutralife.com</a>
                            </p>
                            <small>We'll respond within 24 hours</small>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fa fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Call Us</h4>
                            <p><a href="tel:+919211798913">+91 92117 98913</a></p>
                            <small>Mon - Fri: 9:00 AM - 6:00 PM</small>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Business Hours</h4>
                            <p>
                                Monday - Friday: 9:00 AM - 6:00 PM<br />
                                Saturday: 10:00 AM - 4:00 PM<br />
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="contact-cta text-center">
                    <h3>Ready to Transform Your Fitness Journey?</h3>
                    <p>
                        Join thousands of satisfied customers who trust Ge More
                        Nutralife for their supplement needs.
                    </p>
                    <a href="{{ route('store') }}" class="btn btn-secondary mt-3">
                        Start Shopping Now
                    </a>
                    <div class="cta-stats">
                        <div class="stat-item">
                            <h4>100+</h4>
                            <p>Experimental Tests</p>
                        </div>
                        <div class="stat-item">
                            <h4>5+</h4>
                            <p>Premium Products</p>
                        </div>
                        <div class="stat-item">
                            <h4>95%</h4>
                            <p>Fitness Results</p>
                        </div>
                        <div class="stat-item">
                            <h4>100%</h4>
                            <p>Quality Assured</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('additional_js')
<script>
// Featured Products functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize featured products display
    console.log('Featured products loaded');
});

// Contact form functionality
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch('/api/contact', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        const messagesDiv = document.getElementById('form-messages');
        messagesDiv.style.display = 'block';
        
        if (data.success) {
            messagesDiv.innerHTML = '<div class="alert alert-success">' + (data.message || 'Message sent successfully!') + '</div>';
            this.reset();
        } else {
            messagesDiv.innerHTML = '<div class="alert alert-danger">' + (data.error || 'Something went wrong. Please try again.') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const messagesDiv = document.getElementById('form-messages');
        messagesDiv.style.display = 'block';
        messagesDiv.innerHTML = '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
    });
});

// Product interaction functions
function addToCart(productId) {
    // Get product details from the page or make an API call
    fetch(`/api/products/${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.data;
                const defaultVariant = product.active_variants.find(v => v.is_default) || product.active_variants[0];
                
                if (!defaultVariant) {
                    alert('Product variant not available');
                    return;
                }
                
                const cartItem = {
                    productId: product.id,
                    variantId: defaultVariant.id,
                    name: product.name,
                    variant: `${defaultVariant.name} - ${defaultVariant.size}`,
                    price: parseFloat(defaultVariant.price),
                    originalPrice: defaultVariant.original_price ? parseFloat(defaultVariant.original_price) : null,
                    image: product.images && product.images.length > 0 ? `/images/${product.images[0]}` : '/images/placeholder.png',
                    quantity: 1
                };
                
                // Get existing cart
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                // Check if item already exists
                const existingItemIndex = cart.findIndex(item => 
                    item.productId === cartItem.productId && item.variantId === cartItem.variantId
                );
                
                if (existingItemIndex !== -1) {
                    cart[existingItemIndex].quantity += 1;
                } else {
                    cart.push(cartItem);
                }
                
                // Save to localStorage
                localStorage.setItem('cart', JSON.stringify(cart));
                
                // Update cart count in header
                updateCartCount();
                
                // Show success message
                showNotification('Product added to cart!', 'success');
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            alert('Error adding product to cart');
        });
}

function buyNow(productId) {
    // Add to cart first, then redirect to checkout
    addToCart(productId);
    
    // Small delay to ensure cart is updated
    setTimeout(() => {
        window.location.href = '/checkout';
    }, 500);
}

function toggleWishlist(productId) {
    // Get existing wishlist
    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    
    const existingIndex = wishlist.findIndex(item => item.productId === productId);
    
    if (existingIndex !== -1) {
        // Remove from wishlist
        wishlist.splice(existingIndex, 1);
        showNotification('Removed from wishlist', 'info');
    } else {
        // Add to wishlist
        wishlist.push({
            productId: productId,
            addedAt: new Date().toISOString()
        });
        showNotification('Added to wishlist!', 'success');
    }
    
    // Save to localStorage
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    
    // Update wishlist count in header
    updateWishlistCount();
}

function quickView(productId) {
    // This would open a modal with product details
    // For now, redirect to product page
    window.location.href = `/products/${productId}`;
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    // Update cart count in header
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        element.textContent = totalItems;
    });
}

function updateWishlistCount() {
    const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    
    // Update wishlist count in header
    const wishlistCountElements = document.querySelectorAll('.wishlist-count');
    wishlistCountElements.forEach(element => {
        element.textContent = wishlist.length;
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Initialize cart and wishlist counts on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    updateWishlistCount();
});

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .notification {
        animation: slideIn 0.3s ease;
    }
`;
document.head.appendChild(style);
</script>
@endsection 