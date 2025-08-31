@extends('layouts.app')

@section('title', $product->name . ' - Ge More Nutralife')

@section('additional_css')
<style>
.product-detail-page {
    padding: 60px 0;
    background: #f8f9fa;
    min-height: 80vh;
}

.product-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 30px;
}

.product-images {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.product-main-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-thumbnails {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.product-thumbnail {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.3s ease;
}

.product-thumbnail:hover,
.product-thumbnail.active {
    border-color: #8b0000;
}

.product-info {
    padding: 30px;
}

.product-category {
    color: #8b0000;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.product-name {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    line-height: 1.2;
}

.product-description {
    color: #666;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 25px;
}

.product-variants {
    margin-bottom: 25px;
}

.variant-option {
    display: inline-block;
    background: #f8f9fa;
    padding: 8px 16px;
    border-radius: 20px;
    margin: 5px;
    font-size: 0.9rem;
    color: #333;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
}

.variant-option:hover,
.variant-option.selected {
    background: #8b0000;
    color: white;
    border-color: #8b0000;
}

.product-price {
    margin-bottom: 30px;
}

.original-price {
    font-size: 1.5rem;
    color: #999;
    text-decoration: line-through;
    margin-right: 15px;
}

.current-price {
    font-size: 2.5rem;
    font-weight: 700;
    color: #8b0000;
}

.product-actions {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.btn-add-to-cart,
.btn-buy-now {
    padding: 15px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-add-to-cart {
    background: #8b0000;
    color: white;
}

.btn-add-to-cart:hover {
    background: #6b0000;
    transform: translateY(-2px);
}

.btn-buy-now {
    background: #28a745;
    color: white;
}

.btn-buy-now:hover {
    background: #218838;
    transform: translateY(-2px);
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.quantity-input {
    width: 80px;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 5px;
    text-align: center;
    font-size: 1.1rem;
}

.quantity-btn {
    width: 40px;
    height: 40px;
    border: 2px solid #ddd;
    background: white;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    border-color: #8b0000;
    color: #8b0000;
}

.product-features {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid #eee;
}

.features-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.features-list {
    list-style: none;
    padding: 0;
}

.features-list li {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
    color: #666;
}

.features-list li:before {
    content: "✓";
    color: #28a745;
    font-weight: bold;
    margin-right: 10px;
}

.related-products {
    margin-top: 50px;
}

.related-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    text-align: center;
    margin-bottom: 30px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.related-product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.related-product-card:hover {
    transform: translateY(-5px);
}

.related-product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.related-product-info {
    padding: 20px;
}

.related-product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.related-product-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #8b0000;
    margin-bottom: 15px;
}

.related-product-actions {
    display: flex;
    gap: 10px;
}

.btn-related-add-to-cart {
    padding: 8px 16px;
    background: #8b0000;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-related-add-to-cart:hover {
    background: #6b0000;
}

@media (max-width: 768px) {
    .product-actions {
        flex-direction: column;
    }
    
    .product-name {
        font-size: 2rem;
    }
    
    .current-price {
        font-size: 2rem;
    }
}
</style>
@endsection

@section('content')
<div class="product-detail-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product-container">
                    <div class="product-images">
                        @if ($product->images && count($product->images) > 0)
                            <img src="{{ asset('images/' . $product->images[0]) }}" 
                                 alt="{{ $product->name }}" 
                                 class="product-main-image" 
                                 id="main-product-image" />
                            
                            @if (count($product->images) > 1)
                                <div class="product-thumbnails">
                                    @foreach ($product->images as $index => $image)
                                        <img src="{{ asset('images/' . $image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="product-thumbnail {{ $index === 0 ? 'active' : '' }}"
                                             onclick="changeMainImage('{{ asset('images/' . $image) }}', this)" />
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="no-image" style="height: 100%; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                <i class="fas fa-image" style="font-size: 4rem; color: #ddd;"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="product-container">
                    <div class="product-info">
                        <div class="product-category">{{ $product->category->name }}</div>
                        <h1 class="product-name">{{ $product->name }}</h1>
                        <div class="product-description">{{ $product->description }}</div>
                        
                        @if ($product->activeVariants->count() > 0)
                            <div class="product-variants">
                                <h4>Available Variants:</h4>
                                @foreach ($product->activeVariants as $variant)
                                    <span class="variant-option" onclick="selectVariant({{ $variant->id }}, '{{ $variant->name }}', {{ $variant->price }})">
                                        {{ $variant->name }}
                                    </span>
                                @endforeach
                            </div>
                            
                            @php
                                $defaultVariant = $product->activeVariants->where('is_default', true)->first() ?? $product->activeVariants->first();
                            @endphp
                            
                            <div class="product-price" id="product-price">
                                @if ($defaultVariant->original_price && $defaultVariant->original_price > $defaultVariant->price)
                                    <span class="original-price">₹{{ number_format($defaultVariant->original_price, 2) }}</span>
                                @endif
                                <span class="current-price" id="current-price">₹{{ number_format($defaultVariant->price, 2) }}</span>
                            </div>
                            
                            <input type="hidden" id="selected-variant-id" value="{{ $defaultVariant->id }}">
                        @else
                            <div class="product-price">
                                <span class="current-price">Price not available</span>
                            </div>
                        @endif
                        
                        <div class="quantity-selector">
                            <label for="quantity">Quantity:</label>
                            <button class="quantity-btn" onclick="changeQuantity(-1)">-</button>
                            <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="99">
                            <button class="quantity-btn" onclick="changeQuantity(1)">+</button>
                        </div>
                        
                        <div class="product-actions">
                            <button class="btn btn-add-to-cart" onclick="addToCart({{ $product->id }})">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button class="btn btn-buy-now" onclick="buyNow({{ $product->id }})">
                                <i class="fas fa-bolt"></i> Buy Now
                            </button>
                        </div>
                        
                        @if ($product->features && count($product->features) > 0)
                            <div class="product-features">
                                <h3 class="features-title">Key Features</h3>
                                <ul class="features-list">
                                    @foreach ($product->features as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        @if ($relatedProducts->count() > 0)
            <div class="related-products">
                <h2 class="related-title">Related Products</h2>
                <div class="related-grid">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="related-product-card">
                            <img src="{{ $relatedProduct->images && count($relatedProduct->images) > 0 ? asset('images/' . $relatedProduct->images[0]) : asset('images/placeholder.svg') }}" 
                                 alt="{{ $relatedProduct->name }}" 
                                 class="related-product-image" />
                            <div class="related-product-info">
                                <h4 class="related-product-name">{{ $relatedProduct->name }}</h4>
                                @if ($relatedProduct->activeVariants->count() > 0)
                                    @php
                                        $minPrice = $relatedProduct->activeVariants->min('price');
                                    @endphp
                                    <div class="related-product-price">₹{{ number_format($minPrice, 2) }}</div>
                                @endif
                                <div class="related-product-actions">
                                    <button class="btn-related-add-to-cart" onclick="addToCart({{ $relatedProduct->id }})">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('additional_js')
<script src="{{ asset('js/cart-functions.js') }}"></script>
<script>
let selectedVariantId = {{ $product->activeVariants->count() > 0 ? ($product->activeVariants->where('is_default', true)->first() ?? $product->activeVariants->first())->id : 'null' }};
let selectedVariantPrice = {{ $product->activeVariants->count() > 0 ? ($product->activeVariants->where('is_default', true)->first() ?? $product->activeVariants->first())->price : 0 }};

function changeMainImage(imageSrc, thumbnailElement) {
    document.getElementById('main-product-image').src = imageSrc;
    
    // Update active thumbnail
    document.querySelectorAll('.product-thumbnail').forEach(thumb => thumb.classList.remove('active'));
    thumbnailElement.classList.add('active');
}

function selectVariant(variantId, variantName, variantPrice) {
    selectedVariantId = variantId;
    selectedVariantPrice = variantPrice;
    
    // Update price display
    document.getElementById('current-price').textContent = `₹${parseFloat(variantPrice).toFixed(2)}`;
    
    // Update selected variant indicator
    document.querySelectorAll('.variant-option').forEach(option => option.classList.remove('selected'));
    event.target.classList.add('selected');
    
    // Update hidden input
    document.getElementById('selected-variant-id').value = variantId;
}

function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    let newQuantity = parseInt(quantityInput.value) + change;
    
    if (newQuantity < 1) newQuantity = 1;
    if (newQuantity > 99) newQuantity = 99;
    
    quantityInput.value = newQuantity;
}

function addToCart(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    
    if (typeof CartFunctions !== 'undefined') {
        CartFunctions.addToCart(productId, quantity, selectedVariantId);
    } else {
        // Fallback implementation
        const requestBody = { quantity: quantity };
        if (selectedVariantId) {
            requestBody.variant_id = selectedVariantId;
        }
        
        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestBody)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Error adding product to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            showNotification('Error adding product to cart', 'error');
        });
    }
}

function buyNow(productId) {
    const quantity = parseInt(document.getElementById('quantity').value);
    
    if (typeof CartFunctions !== 'undefined') {
        CartFunctions.buyNow(productId, quantity);
    } else {
        // Fallback implementation
        alert('Buy now functionality will be implemented');
    }
}

function showNotification(message, type = 'info') {
    if (typeof CartFunctions !== 'undefined') {
        CartFunctions.showNotification(message, type);
    } else {
        // Fallback notification
        alert(message);
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set first variant as selected by default
    const firstVariant = document.querySelector('.variant-option');
    if (firstVariant) {
        firstVariant.classList.add('selected');
    }
});
</script>
@endsection
